<?php

// Zaman aşımını önlemek için scriptin çalışma süresini uzat
set_time_limit(300);

/**
 * Belirtilen domaini, 'wordlist.txt' dosyasındaki yolları kullanarak tarar.
 *
 * @param string $domain Taranacak domain (örn: example.com)
 * @return array Tarama sonuçlarını içeren bir dizi
 */
function scanDomain(string $domain): array
{
    $wordlistPath = __DIR__ . '/wordlist.txt';
    if (!file_exists($wordlistPath)) {
        return [['path' => 'Hata', 'status' => '', 'type' => 'Kritik Hata', 'description' => 'wordlist.txt bulunamadı.']];
    }

    $paths = file($wordlistPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Güvenlik notuna göre 50 ile sınırla
    $paths = array_slice($paths, 0, 50);

    $results = [];
    $domain = rtrim($domain, '/');

    // Protokol ekle (http/https)
    if (strpos($domain, 'http') !== 0) {
        $domain = 'https://' . $domain;
    }

    foreach ($paths as $path) {
        $url = $domain . $path;
        $result = ['path' => $path, 'status' => '', 'type' => '', 'description' => ''];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true); // Başlıkları almak için
        curl_setopt($ch, CURLOPT_NOBODY, false); // İçeriği almak için
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Yönlendirmeleri takip etme
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PanelGhost-Scanner/1.0');
        // SSL sertifika doğrulamasını atla (lokal veya geçersiz sertifikalar için)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if ($error) {
            $result['status'] = 'Hata';
            $result['type'] = 'Bağlantı Hatası';
            $result['description'] = $error;
            $results[] = $result;
            curl_close($ch);
            continue;
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        $result['status'] = $httpCode;

        switch ($httpCode) {
            case 200:
                $cleanBody = strip_tags($body);
                $bodyLength = strlen(trim($cleanBody));

                if ($bodyLength < 100 || preg_match('/^\s*OK\s*$/i', $cleanBody)) {
                    $result['type'] = 'Ölü Panel';
                    $result['description'] = "{$bodyLength} karakterlik potansiyel boş sayfa.";
                } elseif (preg_match('/(login|admin|username|password|cpanel|kullanıcı|şifre|parola|giriş)/i', $cleanBody)) {
                    $result['type'] = 'Aktif Panel';
                    $result['description'] = 'İçerikte anahtar kelimeler bulundu.';
                } else {
                    $result['type'] = 'Diğer';
                    $result['description'] = 'Sayfa bulundu ancak bilinen bir panel imzası yok.';
                }
                break;
            case 403:
                $result['type'] = 'Engelli';
                $result['description'] = 'Erişim engellendi (Forbidden).';
                break;
            case 301:
            case 302:
                $result['type'] = 'Yönlendirme';
                if (preg_match('/Location: (.*)/i', $header, $matches)) {
                    $result['description'] = 'Yeni adres: ' . trim($matches[1]);
                } else {
                    $result['description'] = 'Sayfa başka bir adrese yönlendiriliyor.';
                }
                break;
            case 404:
                $result['type'] = 'Bulunamadı';
                $result['description'] = 'Bu yolda bir sayfa yok.';
                break;
            default:
                $result['type'] = 'Diğer';
                $result['description'] = "HTTP durum kodu: {$httpCode}";
                break;
        }
        $results[] = $result;
    }

    return $results;
}

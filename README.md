# 👻 PanelGhost - Akıllı Admin Panel Tarayıcısı

PanelGhost, bir web sitesinin olası yönetici paneli yollarını hızlı ve akıllı bir şekilde taramak için geliştirilmiş basit bir PHP aracıdır. Belirtilen domaini, sık kullanılan panel yollarını içeren bir kelime listesiyle test eder, HTTP durum kodlarını analiz eder ve sayfa içeriğini inceleyerek potansiyel giriş panellerini tespit eder.



## 🎯 Temel Özellikler

- **Hızlı Tarama**: `curl_multi` kullanmadan, basit `curl` ile sıralı tarama yapar.
- **Akıllı Analiz**: Sadece HTTP durum kodlarına bakmaz, aynı zamanda sayfa içeriğini de analiz eder:
  - **Aktif Panel**: `login`, `password`, `username` gibi anahtar kelimeler arar.
  - **Ölü Panel**: Çok kısa veya anlamsız içerikleri (örn: "OK") tespit eder.
  - **Engelli Panel**: `403 Forbidden` gibi erişim engellerini işaretler.
  - **Yönlendirme**: `301/302` yönlendirmelerini ve hedef adresi loglar.
- **Modern Arayüz**: Bootstrap 5 ile oluşturulmuş temiz, mobil uyumlu bir arayüze sahiptir.
- **Kolay Kurulum**: Bağımlılık gerektirmez, sadece PHP sunucusu yeterlidir.
- **Genişletilebilir**: `wordlist.txt` dosyasını düzenleyerek kendi tarama yollarınızı ekleyebilirsiniz.

## 📦 Teknolojiler

- **Backend**: PHP 8.0+
- **Frontend**: HTML5, Bootstrap 5
- **HTTP İstekleri**: PHP cURL eklentisi

## 📁 Klasör Yapısı

```
panelghost/
├── index.php         # Ana arayüz ve sonuçların gösterildiği sayfa
├── panelghost.php    # Tarama mantığını içeren ana script
├── wordlist.txt      # Taranacak admin yollarının listesi
├── README.md         # Bu dosya
├── assets/
│   └── style.css     # Arayüz için özel CSS stilleri
├── results/          # Raporların kaydedileceği dizin (gelecek özellik)
└── logs/             # Log dosyaları için (gelecek özellik)
```

## 🚀 Nasıl Kullanılır?

Projeyi yerel makinenizde çalıştırmak çok kolaydır:

1.  **Projeyi Klonlayın:**
    ```bash
    git clone https://github.com/akbak/PanelGhost.git
    cd PanelGhost
    ```

2.  **PHP Dahili Sunucusunu Başlatın:**
    Proje dizinindeyken terminalde aşağıdaki komutu çalıştırın. (PHP'nin sisteminizde kurulu olması gerekir.)
    ```bash
    php -S localhost:8000
    ```

3.  **Tarayıcıda Açın:**
    Web tarayıcınızı açın ve `http://localhost:8000` adresine gidin.

4.  **Taramayı Başlatın:**
    Giriş alanına taramak istediğiniz domaini yazın (örn: `example.com`) ve "Taramayı Başlat" butonuna tıklayın.

## ⚠️ Sorumluluk Reddi

Bu araç yalnızca eğitim amaçlı ve yasal testler için tasarlanmıştır. İzinsiz olarak başkalarına ait sistemler üzerinde tarama yapmak yasa dışı olabilir. Aracın kullanımından doğacak tüm sorumluluk kullanıcıya aittir.

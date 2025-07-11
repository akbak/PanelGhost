<?php
require_once 'panelghost.php';

$domain = '';
$results = [];
$scan_executed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['domain'])) {
    $domain = trim(strip_tags($_POST['domain']));
    // Basit bir domain geçerlilik kontrolü
    if (filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
        $results = scanDomain($domain);
        $scan_executed = true;
    } else {
        $results = [['path' => 'Hata', 'status' => '', 'type' => 'Geçersiz Domain', 'description' => 'Lütfen geçerli bir domain girin.']];
        $scan_executed = true;
    }
}

function getStatusClass($status): string
{
    if ($status >= 200 && $status < 300) return 'status-200';
    if ($status >= 300 && $status < 400) return 'status-301'; // Genel yönlendirme rengi
    if ($status == 403) return 'status-403';
    if ($status == 404) return 'status-404';
    return '';
}

function getTypeBadge($type): string
{
    switch ($type) {
        case 'Aktif Panel': return '<span class="badge badge-aktif">Aktif Panel</span>';
        case 'Engelli': return '<span class="badge badge-engelli">Engelli</span>';
        case 'Ölü Panel': return '<span class="badge badge-olu">Ölü Panel</span>';
        case 'Yönlendirme': return '<span class="badge badge-yonlendirme">Yönlendirme</span>';
        default: return '<span class="badge badge-diger">' . htmlspecialchars($type) . '</span>';
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PanelGhost - Admin Panel Tarayıcı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="text-center my-4">
            <h1 class="display-4">👻 PanelGhost</h1>
            <p class="lead">Akıllı Admin Panel Tarayıcısı</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="index.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" name="domain" placeholder="example.com" value="<?= htmlspecialchars($domain) ?>" required>
                        <button class="btn btn-primary" type="submit">Taramayı Başlat</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($scan_executed): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h4>Tarama Sonuçları: <?= htmlspecialchars($domain) ?></h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Yol (Path)</th>
                                <th>Durum Kodu</th>
                                <th>Panel Türü</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <?php if ($result['type'] !== 'Bulunamadı'): ?>
                                <tr class="<?= getStatusClass($result['status']) ?>">
                                    <td><?= htmlspecialchars($result['path']) ?></td>
                                    <td><strong><?= htmlspecialchars($result['status']) ?></strong></td>
                                    <td><?= getTypeBadge($result['type']) ?></td>
                                    <td><?= htmlspecialchars($result['description']) ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <button class="btn btn-success" disabled>Raporu İndir (Yakında)</button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <footer class="footer">
            <p>&copy; <?= date('Y') ?> PanelGhost. Tüm hakları saklıdır.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

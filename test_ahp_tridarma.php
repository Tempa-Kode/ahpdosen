<?php
// Test script untuk AHP Tridarma
echo "=== Testing AHP Tridarma Controller ===\n";

try {
    // Instantiate controller
    $controller = new App\Http\Controllers\AhpTridarmaController();
    echo "✓ Controller berhasil diinstansiasi\n";

    // Test perhitungan AHP
    echo "\n=== Testing Perhitungan AHP ===\n";
    $response = $controller->perhitunganAhpTridarma();
    $data = json_decode($response->getContent(), true);

    if ($data['status'] === 'success') {
        echo "✓ Perhitungan AHP berhasil\n";
        echo "✓ Total dosen: " . $data['data']['jumlah_dosen'] . "\n";
        echo "✓ Konsistensi: " . $data['data']['konsistensi']['konsisten'] . "\n";
        echo "✓ CR: " . $data['data']['konsistensi']['CR'] . "\n";

        // Show top 3 ranking
        echo "\n=== Top 3 Ranking ===\n";
        for ($i = 0; $i < min(3, count($data['data']['hasil_akhir'])); $i++) {
            $item = $data['data']['hasil_akhir'][$i];
            echo ($i + 1) . ". " . $item['dosen']['nama'] .
                 " - Prioritas: " . $item['prioritas_global'] .
                 " - Persentase: " . $item['persentase'] . "%\n";
        }

        // Test detail dosen
        if (!empty($data['data']['hasil_akhir'])) {
            $firstDosen = $data['data']['hasil_akhir'][0];
            echo "\n=== Testing Detail Dosen ===\n";
            $detailResponse = $controller->detailDosenAhpTridarma($firstDosen['dosen']['id']);
            $detailData = json_decode($detailResponse->getContent(), true);

            if ($detailData['status'] === 'success') {
                echo "✓ Detail dosen berhasil diambil\n";
                echo "✓ Dosen: " . $detailData['data']['dosen']['nama'] . "\n";
                echo "✓ Ranking: #" . $detailData['data']['ranking'] . "\n";
            } else {
                echo "✗ Gagal mengambil detail dosen\n";
            }
        }

    } else {
        echo "✗ Perhitungan AHP gagal: " . ($data['message'] ?? 'Unknown error') . "\n";
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test selesai ===\n";

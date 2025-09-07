<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ranking Dosen Terbaik</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.3;
            padding: 10px;
            font-size: 12px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>') repeat;
            background-size: 30px 30px;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }

        .subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .report-info {
            padding: 10px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            font-size: 11px;
        }

        .date-info {
            color: #6c757d;
            font-weight: 500;
        }

        .total-lecturers {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 11px;
        }

        .table-container {
            padding: 15px;
            overflow-x: auto;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .ranking-table thead {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .ranking-table th {
            padding: 8px 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 10px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .ranking-table th:last-child {
            border-right: none;
        }

        .ranking-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f3f4;
        }

        .ranking-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .ranking-table td {
            padding: 6px 4px;
            text-align: center;
            font-weight: 500;
            font-size: 10px;
        }

        .ranking-table .rank-cell {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            font-weight: 700;
            font-size: 11px;
        }

        .ranking-table .name-cell {
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            max-width: 150px;
            font-size: 9px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ranking-table .score-cell {
            font-weight: 700;
            color: #e74c3c;
            background: linear-gradient(135deg, #fff5f5, #ffe6e6);
        }

        .criteria-cell {
            font-weight: 600;
            color: #495057;
        }

        /* Ranking badges for top 3 */
        .ranking-table tbody tr:nth-child(1) .rank-cell {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #333;
        }

        .ranking-table tbody tr:nth-child(2) .rank-cell {
            background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
            color: #333;
        }

        .ranking-table tbody tr:nth-child(3) .rank-cell {
            background: linear-gradient(135deg, #cd7f32, #daa520);
            color: white;
        }

        /* Top 3 row highlighting */
        .ranking-table tbody tr:nth-child(1) {
            background: linear-gradient(135deg, #fff9c4, #fef7cd);
        }

        .ranking-table tbody tr:nth-child(2) {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .ranking-table tbody tr:nth-child(3) {
            background: linear-gradient(135deg, #fdf2e9, #fbe9d3);
        }

        .footer {
            padding: 10px 15px;
            background: #f8f9fa;
            text-align: center;
            color: #6c757d;
            font-size: 8px;
            border-top: 1px solid #e9ecef;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .table-container {
                padding: 15px;
            }

            .ranking-table th,
            .ranking-table td {
                padding: 12px 8px;
                font-size: 0.9rem;
            }

            .report-info {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }

        /* Print styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
            }

            .ranking-table tbody tr:hover {
                transform: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Dosen Terbaik</h1>
            <p class="subtitle">Sistem Penilaian Analytical Hierarchy Process (AHP)</p>
        </div>

        <div class="table-container">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>NIDN</th>
                        <th>Nama Dosen</th>
                        <th>K001</th>
                        <th>K002</th>
                        <th>K003</th>
                        <th>K004</th>
                        <th>Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td class="rank-cell">{{ $loop->iteration }}</td>
                            <td class="criteria-cell">{{ $item["nidn"] }}</td>
                            <td class="name-cell">{{ $item["nama"] }}</td>
                            <td class="criteria-cell">{{ $item["k001"] }}</td>
                            <td class="criteria-cell">{{ $item["k002"] }}</td>
                            <td class="criteria-cell">{{ $item["k003"] }}</td>
                            <td class="criteria-cell">{{ $item["k004"] }}</td>
                            <td class="score-cell">{{ number_format($item["nilai_decimal"], 3) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p><strong>Keterangan:</strong> K001 = Pendidikan & Pengajaran | K002 = Penelitian | K003 = Pengabdian Kepada Masyarakat | K004 = Kegiatan Penunjang Tridarma</p>
        </div>
    </div>
</body>

</html>

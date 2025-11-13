<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .verification-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        padding: 3rem;
        max-width: 600px;
    }

    .verification-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 3rem;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    </style>
</head>

<body>
    <div class="verification-card">
        <div class="verification-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="text-center mb-3">
            <span class="badge bg-success">
                <i class="fas fa-shield-alt me-2"></i>Tanda Tangan Valid
            </span>
        </h2>
        <p class="text-center text-muted mb-4">Tanda tangan digital telah diverifikasi</p>

        <div class="info-container">
            <div class="info-row">
                <span><i class="fas fa-user me-2"></i>Nama</span>
                <strong>{{ $result['nama_approver'] }}</strong>
            </div>
            <div class="info-row">
                <span><i class="fas fa-briefcase me-2"></i>Role</span>
                <strong>{{ ucwords(str_replace('_', ' ', $result['role'])) }}</strong>
            </div>
            @if($result['jabatan'])
            <div class="info-row">
                <span><i class="fas fa-id-badge me-2"></i>Jabatan</span>
                <strong>{{ $result['jabatan'] }}</strong>
            </div>
            @endif
            <div class="info-row">
                <span><i class="fas fa-calendar-alt me-2"></i>Dibuat</span>
                <strong>{{ $result['created_at']->format('d M Y H:i') }}</strong>
            </div>
        </div>

        <div class="alert alert-success mt-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Signature terverifikasi!</strong> Tanda tangan ini resmi.
        </div>

        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-home me-2"></i>Kembali
            </a>
        </div>
    </div>
</body>

</html>
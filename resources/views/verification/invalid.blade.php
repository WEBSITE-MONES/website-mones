<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Tidak Valid</title>
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
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    </style>
</head>

<body>
    <div class="verification-card">
        <div class="verification-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h2 class="text-center mb-3">
            <span class="badge bg-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Tidak Valid
            </span>
        </h2>
        <p class="text-center text-muted mb-4">Signature tidak ditemukan atau sudah tidak berlaku</p>

        <div class="alert alert-danger">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Peringatan!</strong> Tanda tangan ini tidak dapat diverifikasi.
        </div>

        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-home me-2"></i>Kembali
            </a>
        </div>
    </div>
</body>

</html>
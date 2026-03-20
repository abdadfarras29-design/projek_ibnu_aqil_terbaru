<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Logout - SMP IBNU AQIL</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .logout-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--light-green) 0%, var(--primary-green) 50%, var(--dark-green) 100%);
            padding: 2rem;
        }

        .logout-card {
            background: var(--white);
            border-radius: 20px;
            padding: 3.5rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            max-width: 450px;
            width: 100%;
            text-align: center;
            animation: cardAppear 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .logo-container {
            margin-bottom: 2rem;
        }

        .logo-img-large {
            height: 120px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .warning-icon {
            font-size: 3rem;
            color: #f59e0b;
            margin-bottom: 1.5rem;
            display: block;
        }

        .logout-card h2 {
            color: var(--text-dark);
            font-size: 1.75rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .logout-card p {
            color: var(--text-gray);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            flex: 1;
            border: none;
            display: inline-block;
        }

        .btn-cancel {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .btn-cancel:hover {
            background-color: #e5e7eb;
            transform: translateY(-2px);
        }

        .btn-confirm {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
            filter: brightness(1.1);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Subtle background overlay icons */
        .bg-icon {
            position: absolute;
            z-index: 0;
            opacity: 0.1;
            color: white;
            font-size: 10rem;
            pointer-events: none;
        }

        .icon-1 {
            top: 10%;
            left: 5%;
            transform: rotate(-15deg);
        }

        .icon-2 {
            bottom: 10%;
            right: 5%;
            transform: rotate(15deg);
        }
    </style>
</head>

<body>
    <div class="logout-page">
        <div class="bg-icon icon-1">🎓</div>
        <div class="bg-icon icon-2">📚</div>

        <div class="logout-card">
            <div class="logo-container">
                <img src="Screenshot_2026-02-22-13-16-05-58_1c337646f29875672b5a61192b9010f9.png"
                    alt="Logo SMP IBNU AQIL" class="logo-img-large">
            </div>

            <span class="warning-icon">⚠️</span>

            <h2>Apakah Anda yakin ingin logout?</h2>

            <p>Anda perlu login kembali untuk mengakses akun Anda dan mengelola data sekolah.</p>

            <div class="button-group">
                <a href="javascript:history.back()" class="btn btn-cancel">Batal</a>
                <a href="logout.php" class="btn btn-confirm">Ya, Logout</a>
            </div>
        </div>
    </div>
</body>

</html>
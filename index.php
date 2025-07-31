<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Sistem Manajemen Parfum</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      background-color: #111;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      min-height: 100vh;
    }

    .login-container {
      background: #1a1a1a;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px #333;
      width: 100%;
      max-width: 340px;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #eee;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 4px;
      background: #333;
      color: #fff;
    }

    .form-group input:focus {
      outline: 2px solid #666;
    }

    .btn {
      width: 100%;
      padding: 10px;
      background: #444;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .btn:hover {
      background: #666;
    }

    /* Tambahan opsional jika ingin ada sedikit perbaikan di device kecil */
    @media (max-width: 400px) {
      .login-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <form action="cek-login.php" method="post">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required />
      </div>
      <button type="submit" class="btn">Masuk</button>
    </form>
  </div>
</body>
</html>

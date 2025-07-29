<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <!-- Incluye Bootstrap 5 -->
        <link
            href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <style>
            body {
                background-color: #e0f7ff;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
            }
            .login-card {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                padding: 40px;
                max-width: 350px;
                width: 100%;
                text-align: center;
            }
            .login-card img {
                width: 80px;
                margin-bottom: 20px;
            }
            .form-control {
                border-radius: 30px;
                padding-left: 40px;
            }
            .input-icon {
                position: relative;
            }
            .input-icon i {
                position: absolute;
                top: 50%;
                left: 15px;
                transform: translateY(-50%);
                color: #007bff;
            }
            .btn-login {
                border-radius: 30px;
                background-color: #007bff;
                border: none;
                width: 100%;
                color: white;
                padding: 10px 0;
                font-weight: bold;
            }
            .options {
                display: flex;
                justify-content: space-between;
                font-size: 0.9rem;
                margin-top: 10px;
            }
            .options a {
                color: #007bff;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="login-card">
            <h3 class="text-center mb-3">Ingreso Externo</h3>

            <form method="POST" action="{{ route("ingreso-externo.login") }}">
                @csrf
                <div class="input-icon mb-3">
                    <i class="bi bi-person"></i>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="CUIT"
                        name="cuit"
                        required
                    />
                </div>
                <div class="input-icon mb-3">
                    <i class="bi bi-lock"></i>
                    <input
                        type="password"
                        class="form-control"
                        placeholder="Contraseña"
                        name="clave"
                        required
                    />
                </div>
                <div class="options mb-3">
                    <div>
                        <input type="checkbox" id="recordarme" />
                        <label for="recordarme">Recordarme</label>
                    </div>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
        </div>

        <!-- Incluye Bootstrap Icons y Bootstrap 5 JavaScript -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
            rel="stylesheet"
        />
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

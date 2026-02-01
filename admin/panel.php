<?php
require_once '../config/database.php';

// Verificar si está logueado
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conexion = conectarDB();

// Marcar como leído si se solicita
if (isset($_GET['marcar_leido'])) {
    $id = intval($_GET['marcar_leido']);
    $stmt = $conexion->prepare("UPDATE contactos SET leido = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: panel.php');
    exit;
}

// Eliminar contacto si se solicita
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $stmt = $conexion->prepare("DELETE FROM contactos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: panel.php');
    exit;
}

// Obtener estadísticas
$stats = $conexion->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN leido = 0 THEN 1 ELSE 0 END) as no_leidos,
    SUM(CASE WHEN DATE(fecha_registro) = CURDATE() THEN 1 ELSE 0 END) as hoy
FROM contactos")->fetch_assoc();

// Obtener todos los contactos
$contactos = $conexion->query("SELECT * FROM contactos ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Consultoría</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
        }
        .navbar {
            background: #1a1a2e;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .navbar h1 {
            font-size: 20px;
        }
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background: rgba(255,255,255,0.1);
        }
        .btn-logout {
            background: #e74c3c !important;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 36px;
            color: #1a1a2e;
        }
        .stat-card p {
            color: #666;
            margin-top: 5px;
        }
        .stat-card.nuevo h3 {
            color: #e74c3c;
        }
        .contacts-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            background: #1a1a2e;
            color: white;
            padding: 20px;
        }
        .table-header h2 {
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .no-leido {
            background: #fff3cd !important;
        }
        .no-leido:hover {
            background: #ffe69c !important;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-nuevo {
            background: #e74c3c;
            color: white;
        }
        .badge-leido {
            background: #27ae60;
            color: white;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            display: inline-block;
            margin: 2px;
        }
        .btn-ver {
            background: #3498db;
            color: white;
        }
        .btn-marcar {
            background: #27ae60;
            color: white;
        }
        .btn-eliminar {
            background: #e74c3c;
            color: white;
        }
        .mensaje-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        .modal-body p {
            margin-bottom: 10px;
        }
        .modal-body strong {
            color: #1a1a2e;
        }
        .mensaje-completo {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            white-space: pre-wrap;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
            }
            .table-responsive {
                overflow-x: auto;
            }
            table {
                min-width: 800px;
            }
        }
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #666;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>📊 Panel de Administración</h1>
        <div class="navbar-right">
            <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['admin_nombre']); ?></span>
            <a href="../index.html">Ver Sitio</a>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <!-- Estadísticas -->
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $stats['total']; ?></h3>
                <p>Total de Contactos</p>
            </div>
            <div class="stat-card nuevo">
                <h3><?php echo $stats['no_leidos']; ?></h3>
                <p>Sin Leer</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['hoy']; ?></h3>
                <p>Contactos Hoy</p>
            </div>
        </div>

        <!-- Tabla de contactos -->
        <div class="contacts-table">
            <div class="table-header">
                <h2>📬 Mensajes de Contacto</h2>
            </div>
            <div class="table-responsive">
                <?php if ($contactos->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Servicio</th>
                            <th>Mensaje</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($contacto = $contactos->fetch_assoc()): ?>
                        <tr class="<?php echo $contacto['leido'] ? '' : 'no-leido'; ?>">
                            <td>
                                <?php if ($contacto['leido']): ?>
                                    <span class="badge badge-leido">Leído</span>
                                <?php else: ?>
                                    <span class="badge badge-nuevo">Nuevo</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($contacto['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($contacto['email']); ?></td>
                            <td><?php echo htmlspecialchars($contacto['telefono'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($contacto['servicio_interes'] ?: '-'); ?></td>
                            <td class="mensaje-preview"><?php echo htmlspecialchars($contacto['mensaje']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($contacto['fecha_registro'])); ?></td>
                            <td>
                                <button class="btn btn-ver" onclick="verContacto(<?php echo htmlspecialchars(json_encode($contacto)); ?>)">Ver</button>
                                <?php if (!$contacto['leido']): ?>
                                <a href="?marcar_leido=<?php echo $contacto['id']; ?>" class="btn btn-marcar">✓ Leído</a>
                                <?php endif; ?>
                                <a href="?eliminar=<?php echo $contacto['id']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Eliminar este contacto?')">✗</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <h3>📭 No hay mensajes aún</h3>
                    <p>Los mensajes de contacto aparecerán aquí.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para ver contacto -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📧 Detalle del Contacto</h3>
                <button class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>

    <script>
        function verContacto(contacto) {
            const modal = document.getElementById('modal');
            const body = document.getElementById('modal-body');
            
            body.innerHTML = `
                <p><strong>Nombre:</strong> ${contacto.nombre}</p>
                <p><strong>Email:</strong> <a href="mailto:${contacto.email}">${contacto.email}</a></p>
                <p><strong>Teléfono:</strong> ${contacto.telefono || 'No proporcionado'}</p>
                <p><strong>Empresa:</strong> ${contacto.empresa || 'No proporcionada'}</p>
                <p><strong>Servicio de interés:</strong> ${contacto.servicio_interes || 'No especificado'}</p>
                <p><strong>Fecha:</strong> ${new Date(contacto.fecha_registro).toLocaleString('es-MX')}</p>
                <p><strong>Mensaje:</strong></p>
                <div class="mensaje-completo">${contacto.mensaje}</div>
            `;
            
            modal.classList.add('active');
            
            // Marcar como leído automáticamente
            if (!contacto.leido) {
                fetch('?marcar_leido=' + contacto.id);
            }
        }
        
        function cerrarModal() {
            document.getElementById('modal').classList.remove('active');
        }
        
        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') cerrarModal();
        });
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });
    </script>
</body>
</html>
<?php $conexion->close(); ?>

<?php
session_start();

// Función para guardar proyectos en la sesión
function guardarProyecto($titulo, $descripcion, $imagen) {
    if (!isset($_SESSION['proyectos'])) {
        $_SESSION['proyectos'] = [];
    }
    
    $nuevoProyecto = [
        'id' => time(),
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'imagen' => $imagen,
        'fecha_creacion' => date('Y-m-d H:i:s')
    ];
    
    array_push($_SESSION['proyectos'], $nuevoProyecto);
}

// Función para eliminar un proyecto
function eliminarProyecto($id) {
    if (isset($_SESSION['proyectos'])) {
        $_SESSION['proyectos'] = array_filter($_SESSION['proyectos'], function($proyecto) use ($id) {
            return $proyecto['id'] != $id;
        });
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'guardar':
                $titulo = $_POST['titulo'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $imagen = '';
                
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $imagen = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));
                    $imagen = 'data:image/' . $_FILES['imagen']['type'] . ';base64,' . $imagen;
                }
                
                guardarProyecto($titulo, $descripcion, $imagen);
                break;
                
            case 'eliminar':
                $id = $_POST['id'] ?? 0;
                eliminarProyecto($id);
                break;
        }
    }
    
    // Redirigir para evitar reenvío del formulario
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog de proyectos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>Blog de proyectos</h1>
            </div>
        </nav>
    </header>

    <main>
        <div class="main-content">
            <div class="center-button-container">
                <button id="openModalBtn" class="new-blog-button">
                    Subir Proyecto
                </button>
            </div>

            <div class="projects-section">
                <section class="blogs-grid">
                    <?php if (isset($_SESSION['proyectos']) && !empty($_SESSION['proyectos'])): ?>
                        <?php foreach ($_SESSION['proyectos'] as $proyecto): ?>
                            <article class="blog-card">
                                <div class="blog-content">
                                    <div class="blog-header">
                                        <h3><?php echo htmlspecialchars($proyecto['titulo']); ?></h3>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $proyecto['id']; ?>">
                                            <button type="submit" class="delete-project-button">
                                                <i class="fas fa-trash"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                    <p><?php echo htmlspecialchars($proyecto['descripcion']); ?></p>
                                    <?php if (!empty($proyecto['imagen'])): ?>
                                        <img src="<?php echo $proyecto['imagen']; ?>" alt="<?php echo htmlspecialchars($proyecto['titulo']); ?>">
                                    <?php endif; ?>
                                    <div class="blog-footer">
                                        <small class="fecha">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($proyecto['fecha_creacion'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

    <!-- Modal del formulario -->
    <div id="blogModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Subir Proyecto</h2>
                <button class="close-modal">&times;</button>
            </div>
            <form id="blogForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="guardar">
                <div class="form-group">
                    <label for="blogTitle">Título del Proyecto</label>
                    <input type="text" id="blogTitle" name="titulo" required placeholder="Ingresa el título de tu proyecto">
                </div>
                <div class="form-group">
                    <label for="blogContent">Descripción del Proyecto</label>
                    <textarea id="blogContent" name="descripcion" required placeholder="Describe tu proyecto en detalle..."></textarea>
                </div>
                <div class="form-group">
                    <label for="blogImage">Imagen del Proyecto</label>
                    <input type="file" id="blogImage" name="imagen" accept="image/*">
                    <small class="file-hint">Formatos aceptados: JPG, PNG, GIF</small>
                </div>
                <button type="submit" class="submit-button">Publicar Proyecto</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="footer-content">
        </div>
    </footer>

    <script>
    // Funcionalidad del modal
    const modal = document.getElementById('blogModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.querySelector('.close-modal');

    openModalBtn.addEventListener('click', () => {
        modal.classList.add('show');
    });

    closeModalBtn.addEventListener('click', () => {
        modal.classList.remove('show');
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('show');
        }
    });
    </script>
</body>
</html> 
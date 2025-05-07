// Array para almacenar los blogs
let blogs = [];

// Elementos del modal
const modal = document.getElementById('blogModal');
const openModalBtn = document.getElementById('openModalBtn');
const closeModalBtn = document.querySelector('.close-modal');

// Función para abrir el modal
function openModal() {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Función para cerrar el modal
function closeModal() {
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Event listeners para el modal
openModalBtn.addEventListener('click', openModal);
closeModalBtn.addEventListener('click', closeModal);

// Cerrar modal al hacer clic fuera del contenido
modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeModal();
    }
});

// Cerrar modal con la tecla Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('show')) {
        closeModal();
    }
});

// Función para cargar blogs desde localStorage
function loadBlogs() {
    const savedBlogs = localStorage.getItem('blogs');
    if (savedBlogs) {
        blogs = JSON.parse(savedBlogs);
        displayBlogs();
    }
}

// Función para guardar blogs en localStorage
function saveBlogs() {
    localStorage.setItem('blogs', JSON.stringify(blogs));
}

// Función para mostrar los blogs
function displayBlogs() {
    const blogsGrid = document.querySelector('.blogs-grid');
    if (!blogsGrid) return;

    blogsGrid.innerHTML = '';
    
    blogs.forEach((blog, index) => {
        const blogCard = document.createElement('div');
        blogCard.className = 'blog-card';
        
        blogCard.innerHTML = `
            ${blog.image ? `<img src="${blog.image}" alt="${blog.title}">` : ''}
            <div class="blog-content">
                <h3>${blog.title}</h3>
                <p>${blog.content.substring(0, 150)}${blog.content.length > 150 ? '...' : ''}</p>
            </div>
        `;
        
        blogsGrid.appendChild(blogCard);
    });
}

// Función para manejar el envío del formulario
function handleFormSubmit(event) {
    event.preventDefault();
    
    const title = document.getElementById('blogTitle').value;
    const content = document.getElementById('blogContent').value;
    const imageFile = document.getElementById('blogImage').files[0];
    
    if (!title || !content) {
        alert('Por favor completa todos los campos requeridos');
        return;
    }
    
    const newBlog = {
        id: Date.now(),
        title,
        content,
        date: new Date().toISOString()
    };
    
    if (imageFile) {
        const reader = new FileReader();
        reader.onload = function(e) {
            newBlog.image = e.target.result;
            addBlog(newBlog);
        };
        reader.readAsDataURL(imageFile);
    } else {
        addBlog(newBlog);
    }
}

// Función para agregar un nuevo blog
function addBlog(blog) {
    blogs.unshift(blog);
    saveBlogs();
    displayBlogs();
    
    // Limpiar el formulario y cerrar el modal
    document.getElementById('blogForm').reset();
    closeModal();
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    loadBlogs();
    
    const form = document.getElementById('blogForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Delete project functionality
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', async function() {
            if (confirm('¿Estás seguro de que deseas eliminar este proyecto?')) {
                const projectId = this.dataset.id;
                try {
                    const response = await fetch('eliminar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${projectId}`
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Eliminar la tarjeta del proyecto del DOM
                        this.closest('.blog-card').remove();
                        
                        // Si no quedan proyectos, recargar la página para ocultar el botón de eliminar todos
                        if (document.querySelectorAll('.blog-card').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Error al eliminar el proyecto');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el proyecto');
                }
            }
        });
    });

    // Delete all projects functionality
    const deleteAllBtn = document.getElementById('deleteAllBtn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', async function() {
            if (confirm('¿Estás seguro de que deseas eliminar todos los proyectos? Esta acción no se puede deshacer.')) {
                try {
                    const response = await fetch('eliminar_todos.php', {
                        method: 'POST'
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Recargar la página para mostrar el estado actualizado
                        location.reload();
                    } else {
                        alert('Error al eliminar los proyectos');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar los proyectos');
                }
            }
        });
    }
}); 
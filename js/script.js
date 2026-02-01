// =====================================================
// SCRIPT PARA FORMULARIO DE CONTACTO
// =====================================================

document.getElementById('formContacto').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnEnviar');
    const alertSuccess = document.getElementById('alertSuccess');
    const alertError = document.getElementById('alertError');
    
    // Ocultar alertas previas
    alertSuccess.style.display = 'none';
    alertError.style.display = 'none';
    
    // Mostrar loading
    btn.disabled = true;
    btn.textContent = 'Enviando...';
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('procesar_contacto.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alertSuccess.style.display = 'block';
            this.reset();
            // Scroll hacia el mensaje
            alertSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            alertError.textContent = '❌ ' + data.message;
            alertError.style.display = 'block';
        }
    } catch (error) {
        alertError.textContent = '❌ Error de conexión. Intenta nuevamente.';
        alertError.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Enviar';
    }
});

document.getElementById("formContacto").addEventListener("submit", function(e) {
  e.preventDefault();

  let nombre = document.getElementById("nombre").value;
  let correo = document.getElementById("correo").value;
  let mensaje = document.getElementById("mensaje").value;

  let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

  usuarios.push({
    nombre: nombre,
    correo: correo,
    mensaje: mensaje,
    fecha: new Date().toLocaleString()
  });

  localStorage.setItem("usuarios", JSON.stringify(usuarios));

  alert("Datos enviados correctamente");
  this.reset();
});

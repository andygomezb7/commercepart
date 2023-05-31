$(document).ready(function() {
  // Mostrar el popup de inicio de sesión al hacer clic en el enlace de inicio de sesión
  $('#login-link').click(function() {
    $('#loginModal').modal('show');
  });

  // Ocultar el popup de inicio de sesión al hacer clic en el botón de cierre
  $('#loginModalClose').click(function() {
    $('#loginModal').modal('hide');
  });
});
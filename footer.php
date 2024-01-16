<!-- Footer -->
<footer class="text-center text-lg-start text-muted bg-dark mt-3">
  <!-- Section: Links  -->
  <section class="">
    <div class="container text-center text-md-start pt-4 pb-4">
      <!-- Grid row -->
      <div class="row mt-3">
        <!-- Grid column -->
        <div class="col-12 col-lg-3 col-sm-12 mb-2">
          <!-- Content -->
          <a target="_blank" class="text-white h2">
            <?php echo $getCompany['nombre']; ?>
          </a>
          <p class="mt-1 text-white">
            © 2023 Copyright
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
<!--         <div class="col-6 col-sm-4 col-lg-2">
          <h6 class="text-uppercase text-white fw-bold mb-2">
            Store
          </h6>
          <ul class="list-unstyled mb-4">
            <li><a class="text-white-50" href="#">About us</a></li>
            <li><a class="text-white-50" href="#">Find store</a></li>
            <li><a class="text-white-50" href="#">Categories</a></li>
            <li><a class="text-white-50" href="#">Blogs</a></li>
          </ul>
        </div> -->
        <!-- Grid column -->

        <!-- Grid column -->
<!--         <div class="col-6 col-sm-4 col-lg-2">
          <h6 class="text-uppercase text-white fw-bold mb-2">
            Information
          </h6>
          <ul class="list-unstyled mb-4">
            <li><a class="text-white-50" href="#">Help center</a></li>
            <li><a class="text-white-50" href="#">Money refund</a></li>
            <li><a class="text-white-50" href="#">Shipping info</a></li>
            <li><a class="text-white-50" href="#">Refunds</a></li>
          </ul>
        </div> -->
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-12 col-sm-12 col-lg-6">
          <!-- Links -->
          <h6 class="text-uppercase text-white fw-bold mb-2">
            Nosotros
          </h6>
          <ul class="list-unstyled mb-4">
            <li><a class="text-white-50" href="#">Contactanos</a></li>
            <li><a class="text-white-50" href="#">Preguntas frecuentes</a></li>
            <li><a class="text-white-50" href="#">Reporta un problema</a></li>
            <li><a class="text-white-50" href="#">Pagos</a></li>
          </ul>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-12 col-sm-12 col-lg-3">
          <!-- Links -->
          <h6 class="text-uppercase text-white fw-bold mb-2">Suscribete</h6>
          <p class="text-white">A todas nuestras ofertas que llegaran mensualmente a tu correo</p>
          <div class="input-group mb-3">
            <input type="email" class="form-control border rounded-0" placeholder="Correo electronico" aria-label="Email" aria-describedby="button-addon2" />
            <button class="btn btn-light border shadow-0 rounded-0" type="button" id="button-addon2" data-mdb-ripple-color="dark">
              Suscribirme
            </button>
          </div>
        </div>
        <!-- Grid column -->
      </div>
      <!-- Grid row -->
    </div>
  </section>
  <!-- Section: Links  -->

  <div class="">
    <div class="container">
      <div class="d-flex justify-content-between py-4 border-top">
        <!--- payment --->
        <div>
          <i class="fab fa-lg fa-cc-visa text-white"></i>
          <!-- <i class="fab fa-lg fa-cc-amex text-white"></i> -->
          <i class="fab fa-lg fa-cc-mastercard text-white"></i>
          <!-- <i class="fab fa-lg fa-cc-paypal text-white"></i> -->
        </div>
        <!--- payment --->
      </div>
    </div>
  </div>
</footer>
<!-- Footer -->

<!-- Modal -->
       <div class="modal fade" id="customModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
           <div class="modal-dialog" role="document">
               <div class="modal-content">
                   <div class="modal-header">
                       <h5 class="modal-title" id="modalTitle"></h5>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                   </div>
                   <div class="modal-body" id="modalDescription"></div>
                   <div class="modal-footer" id="modalButtons"></div>
               </div>
           </div>
       </div>


    <!-- Scripts de Bootstrap -->
    <script src="scripts/core.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>
</html>

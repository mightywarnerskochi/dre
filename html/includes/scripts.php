    <!-- Libraries -->
    <?php $body_class = $body_class ?? ''; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.0/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (strpos($body_class, 'property-details-page') !== false) : ?>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.33/dist/fancybox/fancybox.umd.js" crossorigin="anonymous"></script>
  
      <?php endif; ?>
    <?php $style_version = $style_version ?? '1'; ?>
    <script src="public/js/script.js?v=<?php echo htmlspecialchars($style_version, ENT_QUOTES, 'UTF-8'); ?>" defer></script>

</body>

</html>

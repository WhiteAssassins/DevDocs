
<?php 
$cantidad = $this->db->get('docs');
$cant = $cantidad->num_rows();
?>
<footer class="page-footer font-small blue footer-bottom">


<div class="footer-copyright text-center py-3">© 2021 Copyright:
  <a href="#">WhiteAssassins</a>
    Cantidad de Documentaciones <a href="#"><?php echo $cant;?></a>
</div>



</footer>

</body>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/mdb.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/pro/cards.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/popper.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/pro/dropdown/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/pro/buttons.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/addons-pro/timeline.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/jquery.cookie.js"></script>
    <script>
        var config = {
            base_url: '<?php echo base_url();?>',
            loading_touch_device: 1,
        }
    </script>
    <script type="text/javascript" src="<?php echo base_url('public/'); ?>js/main.js"></script>

</html>
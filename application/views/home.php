<div class="container ct2">
    <div class="row row_dd">
        <center>
        <?php 
            $resultado = $this->db->get('docs');
            $rest = $resultado->result_array(); 
            foreach ($rest as $key) {              
        ?>

            <div class="card z-depth-5 animate__animated animate__swing" style="width: 20rem;">
            
                <img class="card-img-top  animate__animated animate__swing" src="<?php echo base_url(); ?>img/<?php echo $key['imagen']; ?>" alt="Card image cap">
                
                <div class="card-body">
                    <h5 class="card-title animate__animated animate__swing"><?php echo $key['nombre']; ?></h5><span class="badge badge-pill badge-danger badge-home animate__animated animate__swing"><form method="post" action="<?php echo base_url('home/idioma'); ?>"><input type="hidden" name="idioma" value="<?php echo $key['idioma']; ?>"><button class="sort-red" type="submit"><?php echo $key['idioma']; ?></button></form></span>
                    <span class="badge badge-pill badge-info badge-home animate__animated animate__swing">
                        <form method="post" action="<?php echo base_url('home/tipo'); ?>">
                        <input type="hidden" name="tipo" value="<?php echo $key['tipo']; ?>">
                        <button class="sort-blue" type="submit"><?php echo $key['tipo']; ?></button>
                    </form>
                </span>
                    <p class="card-text animate__animated animate__swing"><?php echo $key['descripcion']; ?></p>
                   <form method="post" action="home/visitas"> 
                    <input type="hidden" name="id" value="<?php echo $key['id']; ?>">
                    <input type="hidden" name="direccion" value="<?php echo $key['direccion']; ?>">
                    <input type="hidden" name="visitas" value="<?php echo $key['visitas']; ?>">
                    <button type="submit" href="docs/<?php echo $key['direccion']; ?>" class="btn btn-info btn-rounded waves-effect waves-light animate__animated animate__swing">Ver Documentacion</a></form>
                    
                </div>
                <!p class="visitas">Visitas: <?php echo $key['visitas'];?></p>
            </div>
        <?php
            }
        ?>
         <div id="addchat_app" 
            data-baseurl="<?php echo base_url() ?>"
            data-csrfname="<?php echo $this->security->get_csrf_token_name() ?>"
            data-csrftoken="<?php echo $this->security->get_csrf_hash() ?>"
        ></div>
        </center>
    </div>
</div>
<script  type="module" src="<?php echo base_url('assets/addchat/js/addchat.min.js') ?>"></script>
        <script nomodule src="<?php echo base_url('assets/addchat/js/addchat-legacy.min.js') ?>"></script>
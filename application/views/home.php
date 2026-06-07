<div class="container ct2">
    <div class="row row_dd">
        <center>
        <?php 
            $resultado = $this->db->get('docs');
            $rest = $resultado->result_array(); 
            foreach ($rest as $key) {              
        ?>

            <div class="card z-depth-5" style="width: 20rem;">
            
                <img class="card-img-top" src="<?php echo base_url(); ?>img/<?php echo html_escape($key['imagen']); ?>" alt="<?php echo html_escape($key['nombre']); ?>">
                
                <div class="card-body">
                    <h5 class="card-title"><?php echo html_escape($key['nombre']); ?></h5><span class="badge badge-pill badge-danger badge-home"><form method="post" action="<?php echo base_url('home/idioma'); ?>"><input type="hidden" name="idioma" value="<?php echo html_escape($key['idioma']); ?>"><button class="sort-red" type="submit"><?php echo html_escape($key['idioma']); ?></button></form></span>
                    <span class="badge badge-pill badge-info badge-home">
                        <form method="post" action="<?php echo base_url('home/tipo'); ?>">
                        <input type="hidden" name="tipo" value="<?php echo html_escape($key['tipo']); ?>">
                        <button class="sort-blue" type="submit"><?php echo html_escape($key['tipo']); ?></button>
                    </form>
                </span>
                    <p class="card-text"><?php echo html_escape($key['descripcion']); ?></p>
                   <form method="post" action="home/visitas"> 
                    <input type="hidden" name="id" value="<?php echo (int) $key['id']; ?>">
                    <input type="hidden" name="direccion" value="<?php echo html_escape($key['direccion']); ?>">
                    <input type="hidden" name="visitas" value="<?php echo (int) $key['visitas']; ?>">
                    <button type="submit" class="btn btn-info btn-rounded waves-effect waves-light">Ver documentación</button></form>
                    
                </div>
                <!p class="visitas">Visitas: <?php echo $key['visitas'];?></p>
            </div>
        <?php
            }
        ?>
        </center>
    </div>
</div>

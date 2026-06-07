<div class="container ct2">
    <div class="row row_dd">
        <center>
        <?php 
            $tipo = $this->input->post('tipo', TRUE);
            $resultado = $this->db->get_where('docs', array('tipo' => $tipo));
            $rest = $resultado->result_array(); 
            $cantidad = $resultado->num_rows();
            if($cantidad >= 1){
                $pedido['sms'] = 'Complete todos los campos';
    
            
            foreach ($rest as $key) {              
        ?>

            <div class="card z-depth-5" style="width: 20rem;">
                <img class="card-img-top" src="<?php echo base_url(); ?>img/<?php echo html_escape($key['imagen']); ?>" alt="<?php echo html_escape($key['nombre']); ?>">
                <div class="card-body">
                <h5 class="card-title"><?php echo html_escape($key['nombre']); ?></h5><span class="badge badge-pill badge-danger badge-demas"><form method="post" action="<?php echo base_url()?>home/idioma"><input type="hidden" name="idioma" value="<?php echo html_escape($key['idioma']); ?>"><button class="sort-red" type="submit"><?php echo html_escape($key['idioma']); ?></button></form></span><span class="badge badge-pill badge-info badge-demas"><form method="post" action="<?php echo base_url()?>home/tipo"><input type="hidden" name="tipo" value="<?php echo html_escape($key['tipo']); ?>"><button class="sort-blue" type="submit"><?php echo html_escape($key['tipo']); ?></button></form></span>
                    <p class="card-text"><?php echo html_escape($key['descripcion']); ?></p>
                    <a href="<?php echo base_url(); ?>docs/<?php echo html_escape($key['direccion']); ?>" class="btn btn-info btn-rounded waves-effect waves-light">Ver documentación</a>
                </div>
            </div>
        <?php
            }
        }
        else{
            echo '<center><div class="card z-depth-5" style="width: 20rem;"><div class="card-body"><h5 class="card-title">No se ha encontrado ningún resultado</h5><a href="../home" class="btn btn-info btn-rounded waves-effect waves-light">Volver</a></div></div></div></center>';
        }
        ?>
        
        </center>
    </div>
</div>

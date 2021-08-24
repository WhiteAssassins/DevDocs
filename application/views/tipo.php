<div class="container ct2">
    <div class="row row_dd">
        <center>
        <?php 
            $tipo = $this->input->post('tipo');
            $resultado = $this->db->get_where('docs', array('tipo' => $tipo));
            $rest = $resultado->result_array(); 
            $cantidad = $resultado->num_rows();
            if($cantidad >= 1){
                $pedido['sms'] = 'Complete todos los campos';
    
            
            foreach ($rest as $key) {              
        ?>

            <div class="card z-depth-5" style="width: 20rem;">
                <img class="card-img-top" src="<?php echo base_url(); ?>img/<?php echo $key['imagen']; ?>" alt="Card image cap">
                <div class="card-body">
                <h5 class="card-title"><?php echo $key['nombre']; ?></h5><span class="badge badge-pill badge-danger badge-demas"><form method="post" action="<?php echo base_url()?>home/idioma"><input type="hidden" name="idioma" value="<?php echo $key['idioma']; ?>"><button class="sort-red" type="submit"><?php echo $key['idioma']; ?></button></form></span><span class="badge badge-pill badge-info badge-demas"><form method="post" action="<?php echo base_url()?>home/tipo"><input type="hidden" name="tipo" value="<?php echo $key['tipo']; ?>"><button class="sort-blue" type="submit"><?php echo $key['tipo']; ?></button></form></span>
                    <p class="card-text"><?php echo $key['descripcion']; ?></p>
                    <a href="<?php echo base_url(); ?>docs/<?php echo $key['direccion']; ?>" class="btn btn-info btn-rounded waves-effect waves-light">Ver Documentacion</a>
                </div>
            </div>
        <?php
            }
        }
        else{
            echo '<center><div class="card z-depth-5" style="width: 20rem;"><div class="card-body"><h5 class="card-title">No se a Encontrado Ningun Resultado</h5><a href="../home" class="btn btn-info btn-rounded waves-effect waves-light">Volver</a></div></div></div></center>';
        }
        ?>
        
        </center>
    </div>
</div>
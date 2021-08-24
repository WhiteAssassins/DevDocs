<div class="container ct2">
    <div class="row row_dd">
        <center style="width: 100%;">
        <?php 
            $nombre = $this->input->post('buscar');
            $this->db->like('nombre',$nombre);
            $resultado = $this->db->get('docs');
            $rest = $resultado->result_array(); 
            $cantidad = $resultado->num_rows();
            if($cantidad >= 1){
                
    
            
            foreach ($rest as $key) {              
        ?>

            <div class="card z-depth-5" style="width: 20rem;">
                <img class="card-img-top" src="<?php echo base_url(); ?>img/<?php echo $key['imagen']; ?>" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $key['nombre']; ?></h5><span class="badge badge-pill badge-danger"><?php echo $key['idioma']; ?></span><span class="badge badge-pill badge-info"><?php echo $key['tipo']; ?></span>
                    <p class="card-text"><?php echo $key['descripcion']; ?></p>
                    <a href="<?php echo base_url(); ?>docs/<?php echo $key['direccion']; ?>" class="btn btn-info btn-rounded waves-effect waves-light">Ver Documentacion</a>
                </div>
            </div>
        <?php
            }
        }
        else{
            echo '<span class="no_result" style="width: 100%;text-align: center;"><i class="fa fa-info"></i><br><span>No se a Encontrado Ningun Resultado</span> <br><a class="btn btn-info" href="../">Regresar</a></span>';
        }
        ?>
        
        </center>
    </div>
</div>
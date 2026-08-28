<?php
class Traslado {
    public string $paciente;
    public string $origen;
    public string $destino;
    public string $estado;
    public function __construct(string $paciente, string $origen, string $destino, string $estado = 'Solicitado') {
        $this->paciente = $paciente;
        $this->origen = $origen;
        $this->destino = $destino;
        $this->estado = $estado;
    }
}
 ?>

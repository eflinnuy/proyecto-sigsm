<?php
class TrasladoDAO {
    public function __construct(private PDO $conexion) {}
    public function listar(string $q=''):array { if($q!==''){ $s=$this->conexion->prepare('SELECT * FROM traslados WHERE paciente LIKE ? OR destino LIKE ? OR estado LIKE ? ORDER BY id DESC'); $s->execute(["%$q%","%$q%","%$q%"]); } else $s=$this->conexion->query('SELECT * FROM traslados ORDER BY id DESC'); return $s->fetchAll(PDO::FETCH_ASSOC); }
    public function buscar(int $id):?array { $s=$this->conexion->prepare('SELECT * FROM traslados WHERE id=?'); $s->execute([$id]); $r=$s->fetch(PDO::FETCH_ASSOC); return $r?:null; }
    public function crear(array $v):void { $s=$this->conexion->prepare('INSERT INTO traslados(paciente,chofer,enfermero,vehiculo,origen,destino,salida,llegada,estado) VALUES(?,?,?,?,?,?,?,?,?)'); $s->execute([$v['paciente'],$v['chofer'],$v['enfermero'],$v['vehiculo'],$v['origen'],$v['destino'],$v['salida']?:null,$v['llegada']?:null,$v['estado']]); }
    public function actualizar(int $id,array $v):void { $s=$this->conexion->prepare('UPDATE traslados SET paciente=?,chofer=?,enfermero=?,vehiculo=?,origen=?,destino=?,salida=?,llegada=?,estado=? WHERE id=?'); $s->execute([$v['paciente'],$v['chofer'],$v['enfermero'],$v['vehiculo'],$v['origen'],$v['destino'],$v['salida']?:null,$v['llegada']?:null,$v['estado'],$id]); }
    public function total():int { return (int)$this->conexion->query('SELECT COUNT(*) FROM traslados')->fetchColumn(); }
}

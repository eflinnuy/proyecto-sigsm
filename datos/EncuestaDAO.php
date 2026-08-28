<?php
class EncuestaDAO {
    public function __construct(private PDO $conexion) {
    }
    public function activas() : array {
        return $this->conexion->query('SELECT * FROM encuestas_config WHERE activa=1 ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar() : array {
        return $this->conexion->query('SELECT e.*,COUNT(r.id) respuestas,ROUND(AVG(r.puntaje),2) promedio FROM encuestas_config e LEFT JOIN encuestas r ON r.encuesta_id=e.id GROUP BY e.id ORDER BY e.id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarActiva(int $id) : ? array {
        $s = $this->conexion->prepare('SELECT * FROM encuestas_config WHERE id=? AND activa=1');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }
    public function crear(string $n, string $d) : void {
        $s = $this->conexion->prepare('INSERT INTO encuestas_config(nombre,descripcion,activa) VALUES(?,?,1)');
        $s->execute([$n, $d]);
    }
    public function cambiarEstado(int $id) : void {
        $s = $this->conexion->prepare('UPDATE encuestas_config SET activa=IF(activa=1,0,1) WHERE id=?');
        $s->execute([$id]);
    }
    public function responder(int $id, int $p, string $c) : void {
        $s = $this->conexion->prepare('INSERT INTO encuestas(encuesta_id,puntaje,comentario) VALUES(?,?,?)');
        $s->execute([$id, $p, $c]);
    }
    public function total() : int {
        return (int) $this->conexion->query('SELECT COUNT(*) FROM encuestas_config')->fetchColumn();
    }
}

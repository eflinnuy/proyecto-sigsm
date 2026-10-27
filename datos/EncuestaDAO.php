<?php
/** Acceso a datos de encuestas y sus respuestas. */
class EncuestaDAO {
    public function __construct(private PDO $pdo) {}
    public function activas(): array {
        return $this->pdo->query('SELECT e.*,s.nombre AS servicio FROM encuestas_config e LEFT JOIN servicios s ON s.id=e.servicio_id WHERE e.activa=1 ORDER BY e.nombre')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar(string $q = ''): array {
        $sql = 'SELECT e.*,s.nombre AS servicio,COUNT(r.id) respuestas,ROUND(AVG(r.puntaje),2) promedio,SUM(r.puntaje=1) p1,SUM(r.puntaje=2) p2,SUM(r.puntaje=3) p3,SUM(r.puntaje=4) p4,SUM(r.puntaje=5) p5 FROM encuestas_config e LEFT JOIN servicios s ON s.id=e.servicio_id LEFT JOIN encuestas r ON r.encuesta_id=e.id';
        if ($q !== '') {
            $stmt = $this->pdo->prepare($sql.' WHERE e.nombre LIKE ? OR s.nombre LIKE ? GROUP BY e.id,s.nombre ORDER BY e.id DESC');
            $stmt->execute(["%$q%", "%$q%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->pdo->query($sql.' GROUP BY e.id,s.nombre ORDER BY e.id DESC')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarActiva(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM encuestas_config WHERE id=? AND activa=1');
        $stmt->execute([$id]); $row=$stmt->fetch(PDO::FETCH_ASSOC); return $row ?: null;
    }
    public function crear(string $nombre,string $pregunta,string $descripcion,?int $servicioId=null): void {
        $stmt=$this->pdo->prepare('INSERT INTO encuestas_config(nombre,pregunta,descripcion,servicio_id,activa) VALUES(?,?,?,?,1)');
        $stmt->execute([$nombre,$pregunta,$descripcion,$servicioId]);
    }
    public function cambiarEstado(int $id): void {
        $stmt=$this->pdo->prepare('UPDATE encuestas_config SET activa=IF(activa=1,0,1) WHERE id=?'); $stmt->execute([$id]);
    }
    public function responder(int $id,int $puntaje,string $comentario,string $token): void {
        $stmt=$this->pdo->prepare('INSERT INTO encuestas(encuesta_id,puntaje,comentario,token_envio) VALUES(?,?,?,?)'); $stmt->execute([$id,$puntaje,$comentario,$token]);
    }
    public function servicios(): array { return $this->pdo->query('SELECT id,nombre FROM servicios ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC); }
    public function total(): int { return (int)$this->pdo->query('SELECT COUNT(*) FROM encuestas_config')->fetchColumn(); }
}

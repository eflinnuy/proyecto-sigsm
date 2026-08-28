<?php
class DocumentoDAO {
    public function __construct(private PDO $conexion) {
    }
    public function listarActivos(string $q = '') : array {
        if ($q !== '') {
            $s = $this->conexion->prepare('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.activo=1 AND (d.titulo LIKE ? OR c.nombre LIKE ?) ORDER BY d.id DESC');
            $s->execute(["%$q%", "%$q%"]);
        } else $s = $this->conexion->query('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.activo=1 ORDER BY d.id DESC');
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscar(int $id) : ? array {
        $s = $this->conexion->prepare('SELECT d.*,c.nombre categoria FROM documentos d JOIN categorias c ON c.id=d.categoria_id WHERE d.id=? AND d.activo=1');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }
    public function categorias() : array {
        return $this->conexion->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
    }
    public function crear(string $t, int $c, string $d, string $a) : void {
        $s = $this->conexion->prepare('INSERT INTO documentos(titulo,categoria_id,descripcion,archivo) VALUES(?,?,?,?)');
        $s->execute([$t, $c, $d, $a]);
    }
    public function actualizar(int $id, string $t, int $c, string $d, string $a) : void {
        $s = $this->conexion->prepare('UPDATE documentos SET titulo=?,categoria_id=?,descripcion=?,archivo=? WHERE id=?');
        $s->execute([$t, $c, $d, $a, $id]);
    }
    public function eliminar(int $id) : void {
        $s = $this->conexion->prepare('UPDATE documentos SET activo=0 WHERE id=?');
        $s->execute([$id]);
    }
    public function totalActivos() : int {
        return (int) $this->conexion->query('SELECT COUNT(*) FROM documentos WHERE activo=1')->fetchColumn();
    }
}

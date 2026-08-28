<?php
class UsuarioDAO {
    public function __construct(private PDO $conexion) {
    }
    public function buscarPorUsuario(string $usuario) : ? array {
        $s = $this->conexion->prepare('SELECT id,nombre,usuario,clave,rol FROM usuarios WHERE usuario=? AND activo=1 LIMIT 1');
        $s->execute([$usuario]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }
}

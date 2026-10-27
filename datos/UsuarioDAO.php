<?php
/**
 * Data Access Object (DAO) de usuarios.
 * La consulta devuelve solamente usuarios activos para el proceso
 * de autenticación.
 */
class UsuarioDAO {
    public function __construct(private PDO $pdo) {
    }
    public function buscarPorUsuario(string $usuario) : ? array {
        $s = $this->pdo->prepare('SELECT id,nombre,usuario,clave,rol FROM usuarios WHERE usuario=? AND activo=1 LIMIT 1');
        $s->execute([$usuario]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }
}

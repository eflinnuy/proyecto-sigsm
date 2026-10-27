<?php
/**
 * Data Access Object (DAO) de traslados.
 * Centraliza las operaciones de consulta, alta, modificación y conteo.
 */
class TrasladoDAO {
    public function __construct(private PDO $pdo) {
    }
    public function listar(string $q = '') : array {
        if ($q !== '') {
            $s = $this->pdo->prepare('SELECT * FROM traslados WHERE paciente LIKE ? OR destino LIKE ? OR estado LIKE ? ORDER BY id DESC');
            $s->execute(["%$q%", "%$q%", "%$q%"]);
        } else $s = $this->pdo->query('SELECT * FROM traslados ORDER BY id DESC');
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscar(int $id) : ? array {
        $s = $this->pdo->prepare('SELECT * FROM traslados WHERE id=?');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }
    public function crear(array $v) : void {
        $s = $this->pdo->prepare('INSERT INTO traslados(paciente,chofer,enfermero,vehiculo,origen,destino,salida,llegada,estado) VALUES(?,?,?,?,?,?,?,?,?)');
        $s->execute([$v['paciente'], $v['chofer'], $v['enfermero'], $v['vehiculo'], $v['origen'], $v['destino'], $v['salida'] ?: null, $v['llegada'] ?: null, $v['estado']]);
    }
    public function actualizar(int $id, array $v) : void {
        $s = $this->pdo->prepare('UPDATE traslados SET paciente=?,chofer=?,enfermero=?,vehiculo=?,origen=?,destino=?,salida=?,llegada=?,estado=? WHERE id=?');
        $s->execute([$v['paciente'], $v['chofer'], $v['enfermero'], $v['vehiculo'], $v['origen'], $v['destino'], $v['salida'] ?: null, $v['llegada'] ?: null, $v['estado'], $id]);
    }
    public function total() : int {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM traslados')->fetchColumn();
    }
}

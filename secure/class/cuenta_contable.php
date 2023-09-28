<?php
class CuentaContable {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function agregarCuentaContable($nombre, $tipo, $cuentaContablePadreID) {
        // Realiza la inserción en la base de datos
        $query = "INSERT INTO cuenta_contable (NombreCuenta, TipoCuenta, CuentaContablePadreID) 
                  VALUES ('$nombre', '$tipo', '$cuentaContablePadreID')";
        
        return $this->db->query($query);
    }

    public function editarCuentaContable($id, $nombre, $tipo, $cuentaContablePadreID) {
        // Realiza la actualización en la base de datos
        $query = "UPDATE cuenta_contable SET NombreCuenta = '$nombre', TipoCuenta = '$tipo', CuentaContablePadreID = '$cuentaContablePadreID' WHERE ID = '$id'";
        
        return $this->db->query($query);
    }

    public function eliminarCuentaContable($id) {
        // Realiza la eliminación en la base de datos
        $query = "DELETE FROM cuenta_contable WHERE ID = '$id'";
        
        return $this->db->query($query);
    }

    public function obtenerCuentaContablePorID($id) {
        $query = "SELECT * FROM cuenta_contable WHERE ID = '$id'";
        $result = $this->db->query($query);

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function get_accounting_plan_numaration($name, $id, $array_numeration) {
        $key = $name . ':' . $id;
        if ( isset($array_numeration[ $key ]) ) {
            return $array_numeration[ $key ]['prefix'] . ' ' . $name;
        }
        return $name;
    }

    public function _repace_if_empty($val, $replace) {
        if (!isset($val) || empty($val)) {
            return $replace;
        }
        return $val;
    }

    function set_numeration_accounting_plan() {
        $db_accounting_account = $this->get_array_accounting_plan(NULL, true);
        $db_aux = array();
        $ingresos = $db_accounting_account[INGRESO_ID];
        $egresos = $db_accounting_account[EGRESOS_ID];
        $db_aux[$ingresos['id']] = $ingresos;
        $db_aux[$ingresos['id']] = $egresos;
        $output = array();
        $this->_set_numeration_accounting_plan($db_aux, $output);
        return $output;
    }

    function get_array_accounting_plan($id = NULL, $index = false) {
        if ($id == NULL) {
            $where = "WHERE CuentaContablePadreID IS NULL AND empresa_id = " . $_SESSION['empresa_id'];
        } else {
            $where = "WHERE CuentaContablePadreID = $id AND empresa_id = " . $_SESSION['empresa_id'];
        }
        $db_accounting_plan = $this->db->query("SELECT * FROM cuenta_contable {$where} ORDER BY CuentaContablePadreID, NombreCuenta ASC");
        $to_return = array();
        if ( empty($db_accounting_plan) ) {
            return $to_return;
        } else {
            $i = 1; foreach ($db_accounting_plan as $plan) {
                $id = $plan['ID'];
                $parent = $plan['CuentaContablePadreID'];
                $name = $plan['NombreCuenta'];
                $to_return[($index ? $i : $id)] = array(
                    'label' => $name,
                    'parent' => $parent,
                    'id' => $id,
                );
                $to_return[($index ? $i : $id)]['childs'] = $this->get_array_accounting_plan($id);
                $i++;
            }
        }
        return $to_return;
    }

    function _set_numeration_accounting_plan($options, &$output, $level = 0, $base_index = '') {
        $next_level = $level + 1;
        if ( !empty($options) ) {
            $index = 1;
            foreach ($options as $key => $o) {
                $is_total = 'total' == $key;
                $label = $o['label'];
                $text_index = $base_index . $index . '.';
                $key = $label . ':' . $o['id'];
                $output[ $key ] = array(
                    'label' => $label,
                    'prefix' => $text_index,
                );
                if ( !empty($o['childs']) ) {
                    $this->_set_numeration_accounting_plan($o['childs'], $output, $next_level, $text_index);
                }
                $index++;
            }
        }
    }

    function get_space_entity_html($number = 4) {
        $output = '';
        for ($i=0; $i < $number - 1; $i++) { 
            $output = $output . '&nbsp;';
        }
        return $output;
    }

    function get_select_options_accounting_plan($default = '', $childs = NULL, $level = 0, $base_index = '') {
        if ($childs == NULL) {
            $options = $this->get_array_accounting_plan();
        } else {
            $options = $childs;
        }
        $output = '';
        $next_level = $level + 1;
        if ( !empty($options) ) {
            $index = 1;
            foreach ($options as $key => $o) {
                $label = $o['label'];
                $space = $this->get_space_entity_html($level * 4);
                $selected = '';
                if ($default == $key) { $selected = 'selected'; }
                $text_index = $base_index . $index . '.';
                $output = $output . "<option value='{$key}' $selected>{$space}{$text_index} {$label}</option>";
                if ( !empty($o['childs']) ) {
                    $output = $output . $this->get_select_options_accounting_plan($default, $o['childs'], $next_level, $text_index);
                }
                $index++;
            }
        }
        return $output;
    }

    // Agrega aquí otros métodos y funciones relacionados con las cuentas contables
}
?>

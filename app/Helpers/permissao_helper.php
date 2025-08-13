<?php
// app/Helpers/PermissaoHelper.php

if (! function_exists('getTipoUsuario')) {
    function getTipoUsuario()
    {
        $session = session();
        return $session->get('tipo');
    }
}

if (! function_exists('getEstadoUsuario')) {
    function getEstadoUsuario()
    {
        $session = session();
        return $session->get('estado');
    }
}

if (! function_exists('getUnidadeUsuario')) {
    function getUnidadeUsuario()
    {
        $session = session();
        return $session->get('unidade');
    }
}

if (! function_exists('usuarioEhAdmin')) {
    function usuarioEhAdmin()
    {
        return getTipoUsuario() === 'admin';
    }
}

if (! function_exists('usuarioEhSupervisor')) {
    function usuarioEhSupervisor()
    {
        return getTipoUsuario() === 'supervisor';
    }
}

if (! function_exists('usuarioEhTecnico')) {
    function usuarioEhTecnico()
    {
        return getTipoUsuario() === 'tecnico';
    }
}

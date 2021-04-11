<?php
    $destinatary = $_POST["address"];

    mail($destinatary, "Teste de e-mail", "Resultado da checklist.");
?>
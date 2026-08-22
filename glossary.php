<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) )
    $_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );

if ( isGetRequest() ) {

    $countries = array();

    $stmt = $pdo->query( 'SELECT * FROM countries' );
    $rows = $stmt->fetchAll();

    foreach ( $rows as $row ) {

        $countries[$row['pk']] = array( 
            'country' => $row['country'],
            'capital' => $row['capital'],
            'src' => 'static/images/' . $row['code'] . '.png'
        );
        $countries[$row['pk']]['homonym'] = isset( $row['hint'] ) ? TRUE : FALSE;
    }

    # TODO - move this table to utilties
    $countryList = '<table id="glossary" class="table table-striped table-hover float-end">
        <thead>
            <tr>
                <th scope="col" class="th-sm">Flag</th>
                <th scope="col">Country</th>
                <th scope="col">Capital</th>
                <th scope="col">Homonym</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($countries as $pk => $country) {

        $homonym = ( $country['homonym'] ) ? '<i class="fa-solid fa-check"></i>' : '';
        $countryList .= '
            <tr>
                <td class="flag-cell">
                    <img src="' . $country['src'] . '" 
                        class="flag-cell text-center align-middle">
                </td>
                <td>' . $country['country'] . '</td>
                <td>' . $country['capital'] . '</td>
                <td class="text-center align-middle text-danger fw-bold">'
                    . $homonym . 
                '</td>
            </tr>';
    }

    $countryList .= '</tbody>
        </table>';
}

view( 'head', ['title' => 'Login'] ); ?>

<body class="p-5">

<?php require_once __DIR__ . '/src/inc/nav.php'; ?>

<?= $countryList ?>

<script>
$(document).ready(function() {

    function adjustSidenav() {
        const $sidenav = $('#sideNavbar');
        const $infoPane = $('#infoPane');
        const $table = $('#glossary');
        if ( $(window).width() < 890 ) {
            $sidenav.width(0);
            $table.width('90%');
        } else {
            $sidenav.width("21%");
            $table.width("73%");
        }
    }

    $(window).on('resize', adjustSidenav);

    adjustSidenav();
});
</script>

</body>
</html>
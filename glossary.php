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
        $countries[$row['pk']]['hint'] = 
            isset( $row['hint'] ) ? substr($row['hint'], 2) : '';
    }

    if ( isset( $_GET['search'] ) && strlen( $_GET['search'] ) > 0 ) {
        $search = strtolower( $_GET['search'] );
        foreach ( $countries as $key => $country ) {
            if ( ! str_contains( strtolower( $country['country'] ), $search ) &&
                ! str_contains( strtolower( $country['capital'] ), $search ) ) {
                unset( $countries[$key] );
            }
        }
    } else {
        $search = FALSE;
    }

    ### TODO - move this table to utilties
    $countryList = '<table id="glossary"
            class="table table-light table-striped table-hover float-end">
        <thead class="sticky-top">
            <tr>
                <th scope="col" class="th-sm">Flag</th>
                <th scope="col">Country</th>
                <th scope="col">Capital</th>
                <th scope="col">Hint</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($countries as $pk => $country) {

        $countryList .= '
            <tr>
                <td class="flag-cell text-center align-middle">
                    <img src="' . $country['src'] . '" class="flag-cell">
                </td>
                <td>' . $country['country'] . '</td>
                <td>' . $country['capital'] . '</td>
                <td class="text-secondary">' . $country['hint'] . '</td>
            </tr>';
    }

    $countryList .= '</tbody>
        </table>';
}

view( 'head', ['title' => 'Glossary'] ); ?>

<body class="p-3 bg-light">

<?php require_once __DIR__ . '/src/inc/nav.php'; ?>

<main>
    <div>
        <form class="input-group float-end p-3 me-2 w-50" 
            method="get" action="glossary.php">
            <input id="search" class="form-control" type="text" name="search"
                placeholder="Search country or capital">
            <button type="submit" class="btn btn-success muted-link-style fw-bold">
                <i class="fa fa-search"></i>
            </button>
            <a href="glossary.php" class="btn btn-warning link-style pt-2 fw-bold">
                <i class="fa fa-undo"></i>
            </a>
        </form>
    </div>
    <div class="p-3">
        <?= $countryList ?>
    </div>
</main>

<script>
$(document).ready(function() {

    function adjustSidenav() {
        const $sidenav = $('#sideNavbar');
        const $infoPane = $('#infoPane');
        const $table = $('#glossary');
        if ( $(window).width() < 890 ) {
            $sidenav.width(0);
            $table.width('100%');
        } else {
            $sidenav.width("21%");
            $table.width("79.5%");
        }
    }

    $(window).on('resize', adjustSidenav);

    adjustSidenav();
});
</script>

</body>
</html>
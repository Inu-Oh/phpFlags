<aside id="infoPane" class="bg-light">
    <div class="card card-body m-3 p-4 bg-light border-5 border-white rounded-4">
        <div class="card-title fs-5 py-1">
            Rank &nbsp;<span class="card-text fs-6"><?= $_SESSION['level'] ?></span>
        </div>
        <div class="card-title fs-5 py-1">
            Cards &nbsp;<span class="card-text fs-6"><?= $_SESSION['cardsRemaining'] ?></span>
        </div>
        <div class="card-title fs-5 py-1">
            Tested &nbsp;<span class="card-text fs-6"><?= $_SESSION['testedCards'] ?></span>
        </div>
        <div class="card-title fs-5 py-1">
            Score&nbsp;<span class="card-text fs-6">
                
            <?php if ( isset( $_SESSION['username'] ) ) {
                echo number_format(
                    $_SESSION['correctCount'],
                    $decimals = 0,
                    $decimal_separator = "",
                    $thousands_separator = ","
                ) . 
                    '</span>
                </div>
                <div class="card-title fs-5 py-1">Rate&nbsp
                    <span class="card-text fs-6">' . 
                        round( $_SESSION['accuracy'] ) . '%';
            } else {
                echo number_format(
                    $_SESSION['score'],
                    $decimals = 0,
                    $decimal_separator = "",
                    $thousands_separator = ","
                );
            } ?>

            </span>
        </div>
    </div>
</aside>
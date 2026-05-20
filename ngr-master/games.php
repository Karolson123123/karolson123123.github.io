<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_match'])) {
    $opp = $conn->real_escape_string($_POST['opponent']);
    $ngr = intval($_POST['ngr_score']);
    $en = intval($_POST['opp_score']);
    $date = $_POST['match_date'];
    $tour = $conn->real_escape_string($_POST['tournament']);
    
    $conn->query("INSERT INTO matches (opponent, ngr_score, opp_score, match_date, tournament) VALUES ('$opp', $ngr, $en, '$date', '$tour')");
    header("Location: games.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM matches WHERE id=$id");
    header("Location: games.php");
    exit;
}

$stats = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN ngr_score > opp_score THEN 1 ELSE 0 END) as wins FROM matches")->fetch_assoc();
$total = $stats['total'] ?? 0;
$wins = $stats['wins'] ?? 0;
$wr = ($total > 0) ? round(($wins / $total) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ngineers | Mecze</title>
    <link rel="shortcut icon" href="img_vid/maupka.webp" type="image/x-icon">
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/games.css">
    <link rel="stylesheet" href="css/admin.css">
    
    <script src="js/import-components.js" defer></script>
</head>
<body>
    <nav></nav>

    <main style="padding-top: 5rem;">
        <header style="text-align: center; margin-bottom: 3rem;">
            <svg width="100" height="110" viewBox="0 0 200 220" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 8 L185 48 L185 128 Q185 178 100 208 Q15 178 15 128 L15 48 Z" fill="var(--zolty)"/>
                <text x="100" y="120" text-anchor="middle" fill="black" font-weight="bold" font-size="45">NGR</text>
            </svg>
            <h1 style="font-size: 3rem; margin-top: 1rem;">Mecze & Wyniki</h1>
            
            <div style="margin-top: 2rem;">
                <p style="font-size: 1.2rem; color: var(--szary);">Aktualny Win Rate: <span style="color: var(--zolty); font-weight: bold;"><?php echo $wr; ?>%</span></p>
                <math xmlns="http://www.w3.org/1998/Math/MathML" display="block">
                    <mrow>
                        <mi>WR</mi>
                        <mo>=</mo>
                        <mfrac>
                            <mi>W</mi>
                            <mi>N</mi>
                        </mfrac>
                        <mo>⋅</mo>
                        <mn>100</mn>
                        <mo>%</mo>
                    </mrow>
                </math>
            </div>
        </header>

        <section class="admin-panel">
            <h3>Dodaj nowy wynik</h3>
            <form method="POST" class="admin-form" id="matchForm">
                <div style="flex: 2; min-width: 250px;">
                    <input type="text" name="opponent" id="oppInput" placeholder="Nazwa przeciwnika..." required style="width: 100%;">
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="number" name="ngr_score" placeholder="NGR" required style="width: 70px;">
                    <span>:</span>
                    <input type="number" name="opp_score" placeholder="OPP" required style="width: 70px;">
                </div>
                <div style="flex: 1;">
                    <input type="date" name="match_date" required style="width: 100%;">
                </div>
                <input type="text" name="tournament" value="ZSŁ LoLCup 2025" style="width: 200px;">
                <button type="submit" name="add_match" id="sendMessageButton">ZAPISZ</button>
            </form>
        </section>

        <section id="historiameczy" style="max-width: 900px; margin: 0 auto; padding: 1rem;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Historia Rozgrywek</h2>
            <?php
            $result = $conn->query("SELECT * FROM matches ORDER BY match_date DESC");
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
                    $isWin = $row['ngr_score'] > $row['opp_score'];
                    $borderClass = $isWin ? 'win-border' : 'loss-border';
                    $textClass = $isWin ? 'win-text' : 'loss-text';
            ?>
                <div class="mecz-container <?php echo $borderClass; ?>">
                    <div class="mecz">
                        <a href="?delete=<?php echo $row['id']; ?>" class="del-link" onclick="return confirm('Usunąć ten mecz?')">[X]</a>
                        <div class="mecz-content">
                            <h1 style="margin: 0; font-size: 1.6rem;">NGR vs <?php echo htmlspecialchars($row['opponent']); ?></h1>
                            <h2 class="<?php echo $textClass; ?>" style="font-size: 3.5rem; margin: 0.5rem 0;">
                                <?php echo $row['ngr_score']; ?> - <?php echo $row['opp_score']; ?>
                            </h2>
                            <p style="color: var(--szary); margin: 0;">
                                <?php echo $row['match_date']; ?> | <?php echo htmlspecialchars($row['tournament']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
            ?>
                <p style="text-align: center; color: var(--szary);">Brak zapisanych meczów w bazie.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer></footer>

    <script>
        document.getElementById('matchForm').addEventListener('submit', function(e) {
            const opp = document.getElementById('oppInput').value.trim();
            if (opp.length < 2) {
                alert("Nazwa przeciwnika musi mieć co najmniej 2 znaki!");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
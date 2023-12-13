<!DOCTYPE html>
<html lang="en">
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbbelajar";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

$sqlC = 'SELECT count(id) FROM question';
$c = $conn->query($sqlC);
$countRow = '';
while ($row = $c->fetch_assoc()) {
    $countRow = $row;
}

$lastDisplayedId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

$sql = "SELECT * FROM question WHERE id > " . $lastDisplayedId . " ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);
$temp = $result->fetch_assoc();

if (!$temp) {
    $sql = "SELECT * FROM question ORDER BY id ASC LIMIT 1";
    $result = $conn->query($sql);
    $temp = $result->fetch_assoc();
}

$nextDisplayedId = $temp['id'];

$conn->close();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="responsivevoice.js"></script>
</head>

<body>
    <section id="navbar">
        <div class="left">
            <a href="index.html">
                <h1>PemrogramanWeb</h1>
            </a>
        </div>
        <div class="right">
            <ul><a class="active" href="kuis.html"><p>Kuis</p></a></ul>
            <ul><a href="tentang.html"><p>Tentang</p></a></ul>
        </div>
    </section>

    <section id="formpertanyaan">
        <div class="text">
            <h2>Pertanyaan</h2>
            <p><?php echo $temp['pertanyaan']; ?></p>
            <p><?php echo $temp['pilihan1']; ?></p>
            <p><?php echo $temp['pilihan2']; ?></p>
            <p><?php echo $temp['pilihan3']; ?></p>
            <p><?php echo $temp['pilihan4']; ?></p>
            <br>
            <div class="button">
                <div onclick="runSpeechRecognition(<?php echo $nextDisplayedId; ?>)" class="buttoni">
                    <i class="fa-solid fa-microphone" style="color: #000000;"></i>
                </div>
                <div class="action">
                    <p id="action"></p>
                </div>
            </div>
        </div>
        <div class="img">
            <img src="<?php echo $temp['foto']; ?>">
        </div>
    </section>

    <section id="hasil">
        <div id="output">
        </div>
    </section>
    

    <script>
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(function (stream) {
                console.log('You let me use your mic!')
            })
            .catch(function (err) {
                console.log('No mic for you!')
            });
        
        var a = 1;

        function runSpeechRecognition() {
            var output = document.getElementById("output");
            var action = document.getElementById("action");

            var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
            var recognition = new SpeechRecognition();

            recognition.onstart = function () {
                action.innerHTML = "<small>Mic - ON</small>";
            };

            recognition.onspeechend = function () {
                action.innerHTML = "<small>Mic - OFF</small>";
                recognition.stop();
            };

            recognition.onresult = function (event) {
                var transcript = event.results[0][0].transcript;
                var userAnswer = transcript.trim().toLowerCase();
                var correctAnswer = "<?php echo $temp['jawaban']; ?>".trim().toLowerCase();

                if (userAnswer === correctAnswer) {
                    output.innerHTML = "Jawaban Anda Benar!";
                    output.style.color = "green";
                } else {
                    output.innerHTML = "Jawaban Anda Salah. Jawaban yang benar adalah: " + correctAnswer;
                    output.style.color = "red";
                }

                a = a + 1;
                output.classList.remove("hide");
                
                var hasilSection = document.getElementById("hasil");
                hasilSection.style.display = "block";

                setTimeout(() => {
                    var nextQuestionId = <?php echo $nextDisplayedId; ?>;
                    var currentUrl = window.location.href;
                    var nextUrl = currentUrl.split('?')[0] + '?last_id=' + nextQuestionId;
                    window.location.href = nextUrl;
                }, 3000);
            };

            recognition.lang = 'id-ID';
            recognition.start();
            
        }
    </script>
</body>

</html>

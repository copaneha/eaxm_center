<?php
include "../config.php";

// Database se questions fetch karein
$result = mysqli_query($conn, "SELECT * FROM question_bank ORDER BY subject ASC");
$questions = [];
$subjects = [];

while($row = mysqli_fetch_assoc($result)) { 
    $questions[] = $row; 
    $sub = $row['subject'];
    if(!isset($subjects[$sub])) { $subjects[$sub] = []; }
    $subjects[$sub][] = count($questions) - 1; 
}

$totalQuestions = count($questions);
$subjectNames = array_keys($subjects);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional CBT Portal | Session 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #337ab7;
            --ans-green: #27ae60;
            --not-ans-red: #e74c3c;
            --review-purple: #8e44ad;
            --bg-gray: #f4f7f9;
            --text-dark: #2c3e50;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { 
            margin: 0; font-family: 'Roboto', sans-serif; 
            background: var(--bg-gray); height: 100vh; 
            display: flex; flex-direction: column; overflow: hidden; 
        }

        /* --- Header --- */
        header {
            background: #fff; padding: 10px 20px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 2px solid var(--primary); box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .timer-box { 
            background: #d9534f; color: white; padding: 6px 15px; 
            border-radius: 4px; font-weight: bold; font-size: 1.1rem;
        }

        /* --- Subject Tabs --- */
        .subject-bar { 
            background: #e8eff7; display: flex; overflow-x: auto; 
            border-bottom: 1px solid #ccc; white-space: nowrap;
        }
        .sub-tab { 
            padding: 12px 25px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
            border-right: 1px solid #ccc; background: #d9edf7; color: var(--text-dark);
            transition: 0.2s;
        }
        .sub-tab.active { background: var(--primary); color: white; }

        /* --- Main Layout --- */
        .main-container { display: flex; flex: 1; overflow: hidden; position: relative; }

        /* Question Section */
        .question-section { 
            flex: 1; display: flex; flex-direction: column; 
            background: #fff; position: relative; overflow-y: auto;
        }
        .q-header { 
            padding: 15px 20px; background: #f8f9fa; 
            border-bottom: 1px solid #ddd; display: flex; justify-content: space-between;
            font-weight: bold; font-size: 1rem; color: var(--primary);
        }
        .q-content { padding: 25px; flex: 1; overflow-y: auto; }
        .q-text { font-size: 1.15rem; line-height: 1.6; margin-bottom: 25px; color: #333; }

        /* Options Design */
        .options-grid { display: flex; flex-direction: column; gap: 12px; }
        .option-label {
            display: flex; align-items: center; padding: 15px;
            border: 1px solid #ddd; border-radius: 6px; cursor: pointer;
            transition: 0.2s; font-size: 1rem;
        }
        .option-label:hover { background: #f1f7ff; border-color: var(--primary); }
        .option-label input { margin-right: 15px; width: 18px; height: 18px; cursor: pointer; }

        /* --- Sidebar (Palette) --- */
        .sidebar { 
            width: 300px; background: #fff; border-left: 1px solid #ddd;
            display: flex; flex-direction: column;
        }
        .user-info { padding: 15px; text-align: center; background: #fafafa; border-bottom: 1px solid #eee; }
        .user-info img { width: 70px; height: 80px; border: 1px solid #ccc; margin-bottom: 5px; }

        .palette-area { padding: 15px; flex: 1; overflow-y: auto; }
        .palette-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        
        /* Official CBT Shape Buttons */
        .p-btn { 
            width: 40px; height: 38px; border: 1px solid #ccc; background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; cursor: pointer; transition: 0.2s;
            clip-path: polygon(0% 15%, 100% 15%, 100% 100%, 0% 100%); /* Default */
        }
        .p-btn.active { outline: 2px solid orange; border: 2px solid #000; font-weight: bold; }
        .p-btn.answered { 
            background: var(--ans-green); color: white; border: none;
            clip-path: polygon(50% 0%, 100% 25%, 100% 100%, 0% 100%, 0% 25%);
        }
        .p-btn.not-answered { 
            background: var(--not-ans-red); color: white; border: none;
            clip-path: polygon(0% 0%, 100% 0%, 100% 75%, 50% 100%, 0% 75%);
        }
        .p-btn.marked { background: var(--review-purple); color: white; border-radius: 50%; clip-path: none; }

        /* --- Footer Nav --- */
        footer {
            background: white; padding: 12px 20px; border-top: 2px solid #eee;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px;
        }
        .btn-group { display: flex; gap: 8px; }
        .btn { 
            padding: 10px 18px; border: 1px solid #ccc; border-radius: 4px; 
            cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: 0.2s;
        }
        .btn-blue { background: var(--primary); color: white; border: none; }
        .btn-green { background: var(--ans-green); color: white; border: none; }
        .btn-purple { background: var(--review-purple); color: white; border: none; }

        /* --- Mobile Responsiveness --- */
        @media (max-width: 850px) {
            .main-container { flex-direction: column; overflow-y: auto; }
            .sidebar { width: 100%; border-left: none; border-top: 2px solid #ddd; height: auto; }
            .question-section { height: auto; min-height: 450px; }
            header { padding: 8px 10px; }
            .exam-title { display: none; } /* Hide text on small screens */
            footer { position: sticky; bottom: 0; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); }
        }
    </style>
</head>
<body>

<header>
    <div class="exam-title" style="font-weight: bold; color: var(--primary);">CBT PORTAL | EXAMINATION 2026</div>
    <div class="timer-box" id="timer">00:00:00</div>
</header>

<div class="subject-bar">
    <?php foreach($subjectNames as $index => $subName): ?>
        <div class="sub-tab <?php echo $index==0?'active':''; ?>" id="tab-<?php echo $index; ?>" onclick="switchSubject(<?php echo $index; ?>)">
            <?php echo strtoupper($subName); ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="main-container">
    <main class="question-section">
        <div class="q-header">
            <span id="q-label">Question No. 1</span>
            <span id="sub-label" style="font-size: 0.8rem; color: #666;"></span>
        </div>

        <div class="q-content">
            <form id="examForm">
                <?php foreach($questions as $index => $row): ?>
                <div class="q-container" id="qbox-<?php echo $index; ?>" 
                     data-subject="<?php echo array_search($row['subject'], $subjectNames); ?>"
                     style="display: <?php echo $index==0?'block':'none'; ?>;">
                    
                    <div class="q-text">
                        <strong>Question <?php echo ($index+1); ?>:</strong><br>
                        <?php echo htmlspecialchars($row['question']); ?>
                    </div>

                    <div class="options-grid">
                        <?php foreach(['a','b','c','d'] as $o): ?>
                        <label class="option-label">
                            <input type="radio" name="ans[<?php echo $index; ?>]" value="<?php echo strtoupper($o); ?>">
                            <?php echo htmlspecialchars($row['option_'.$o]); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </form>
        </div>

        <footer>
            <div class="btn-group">
                <button class="btn btn-purple" onclick="markReview()">Mark for Review</button>
                <button class="btn" onclick="clearResponse()">Clear Response</button>
            </div>
            <div class="btn-group">
                <button class="btn btn-blue" onclick="moveBack()">Back</button>
                <button class="btn btn-blue" onclick="moveNext()">Save & Next</button>
                <button class="btn btn-green" onclick="submitExam()">Submit Exam</button>
            </div>
        </footer>
    </main>

    <aside class="sidebar">
        <div class="user-info">
            <img src="https://via.placeholder.com/70x80?text=PHOTO" alt="User">
            <div style="font-weight: bold; font-size: 0.9rem;">CANDIDATE: STUDENT USER</div>
        </div>

        <div class="palette-area">
            <div style="font-weight: bold; margin-bottom: 10px; font-size: 0.85rem; color: var(--primary);">Question Palette:</div>
            <div class="palette-grid">
                <?php foreach($questions as $index => $row): ?>
                <button class="p-btn" id="pbtn-<?php echo $index; ?>" 
                        data-subject="<?php echo array_search($row['subject'], $subjectNames); ?>"
                        onclick="showQ(<?php echo $index; ?>)">
                    <?php echo $index + 1; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div style="padding: 10px; font-size: 0.75rem; background: #eee; border-top: 1px solid #ddd;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
                <span>🟩 Answered</span>
                <span>🟥 Not Answered</span>
                <span>🟪 Marked</span>
                <span>⬜ Not Visited</span>
            </div>
        </div>
    </aside>
</div>

<script>
    let currentIdx = 0;
    let currentSubjectIdx = 0;
    const totalQ = <?php echo $totalQuestions; ?>;
    const qStates = new Array(totalQ).fill('not-visited');

    function switchSubject(subIdx) {
        currentSubjectIdx = subIdx;
        document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
        document.getElementById('tab-' + subIdx).classList.add('active');

        // Palette filter
        document.querySelectorAll('.p-btn').forEach(btn => {
            btn.style.display = (btn.getAttribute('data-subject') == subIdx) ? 'flex' : 'none';
        });

        // Jump to first question of this subject
        const firstQ = document.querySelector(`.q-container[data-subject="${subIdx}"]`);
        if(firstQ) showQ(parseInt(firstQ.id.split('-')[1]));
    }

    function showQ(idx) {
        document.querySelectorAll('.q-container').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.p-btn').forEach(el => el.classList.remove('active'));
        
        const targetQ = document.getElementById('qbox-' + idx);
        targetQ.style.display = 'block';
        document.getElementById('pbtn-' + idx).classList.add('active');
        document.getElementById('q-label').innerText = "Question No. " + (idx + 1);
        
        // Update subject tab if jumped via palette
        const subIdx = targetQ.getAttribute('data-subject');
        if(subIdx != currentSubjectIdx) {
            document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + subIdx).classList.add('active');
            currentSubjectIdx = subIdx;
        }

        if(qStates[idx] === 'not-visited') qStates[idx] = 'not-answered';
        currentIdx = idx;
        updatePaletteUI(idx);
    }

    function moveNext() {
        const checked = document.getElementById('qbox-' + currentIdx).querySelector('input:checked');
        qStates[currentIdx] = checked ? 'answered' : 'not-answered';
        updatePaletteUI(currentIdx);
        if(currentIdx < totalQ - 1) showQ(currentIdx + 1);
        else alert("You have reached the end of the test.");
    }

    function moveBack() {
        if(currentIdx > 0) showQ(currentIdx - 1);
    }

    function markReview() {
        qStates[currentIdx] = 'marked';
        updatePaletteUI(currentIdx);
        if(currentIdx < totalQ - 1) showQ(currentIdx + 1);
    }

    function clearResponse() {
        document.getElementById('qbox-' + currentIdx).querySelectorAll('input').forEach(i => i.checked = false);
        qStates[currentIdx] = 'not-answered';
        updatePaletteUI(currentIdx);
    }

    function updatePaletteUI(idx) {
        const btn = document.getElementById('pbtn-' + idx);
        btn.classList.remove('answered', 'not-answered', 'marked');
        if(qStates[idx] !== 'not-visited') btn.classList.add(qStates[idx]);
    }

    function submitExam() {
        if(confirm("Are you sure you want to submit the examination?")) {
            alert("Examination submitted successfully!");
            // Window redirect or AJAX form submit here
        }
    }

    // Timer Implementation
    let timeLeft = 3600; 
    setInterval(() => {
        let h = Math.floor(timeLeft / 3600);
        let m = Math.floor((timeLeft % 3600) / 60);
        let s = timeLeft % 60;
        document.getElementById('timer').innerText = 
            `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        if(timeLeft > 0) timeLeft--;
    }, 1000);

    // Initial Load
    switchSubject(0);


    // Example Timer Logic
let timeRemaining = 3600; // 60 minutes in seconds
const timerInterval = setInterval(() => {
    if (timeRemaining <= 0) {
        clearInterval(timerInterval);
        document.getElementById("examForm").submit(); // Automatic Submit
    } else {
        timeRemaining--;
        // Update timer UI here
    }
}, 1000);
</script>
</body>
</html>
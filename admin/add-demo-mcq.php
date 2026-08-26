<?php
require_once '../config.php';

// Get database connection
$pdo = getDB();

try {
    $pdo->beginTransaction();
    
    // Insert demo MCQ test
    $stmt = $pdo->prepare("
        INSERT INTO mcq_tests (title, description, category, level, duration_minutes, total_questions, passing_score, is_published, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        'Islamic Studies Fundamentals',
        'Test your knowledge of basic Islamic concepts, pillars of Islam, and core beliefs.',
        'Islamic Studies',
        'beginner',
        15,
        10,
        70,
        1
    ]);
    
    $testId = $pdo->lastInsertId();
    
    // Insert demo questions
    $questions = [
        [
            'question_text' => 'What is the first pillar of Islam?',
            'option_a' => 'Salah (Prayer)',
            'option_b' => 'Shahada (Declaration of Faith)',
            'option_c' => 'Zakat (Charity)',
            'option_d' => 'Hajj (Pilgrimage)',
            'correct_answer' => 'B',
            'explanation' => 'Shahada is the first pillar of Islam - the declaration that there is no god but Allah and Muhammad is His messenger.'
        ],
        [
            'question_text' => 'How many times a day do Muslims perform Salah?',
            'option_a' => '3 times',
            'option_b' => '4 times',
            'option_c' => '5 times',
            'option_d' => '6 times',
            'correct_answer' => 'C',
            'explanation' => 'Muslims perform Salah five times a day: Fajr, Dhuhr, Asr, Maghrib, and Isha.'
        ],
        [
            'question_text' => 'What is the holy book of Islam?',
            'option_a' => 'Bible',
            'option_b' => 'Torah',
            'option_c' => 'Quran',
            'option_d' => 'Psalm',
            'correct_answer' => 'C',
            'explanation' => 'The Quran is the holy book of Islam, revealed to Prophet Muhammad (peace be upon him).'
        ],
        [
            'question_text' => 'In which month do Muslims fast during Ramadan?',
            'option_a' => 'Shawwal',
            'option_b' => 'Ramadan',
            'option_c' => 'Muharram',
            'option_d' => 'Dhul Hijjah',
            'correct_answer' => 'B',
            'explanation' => 'Ramadan is the ninth month of the Islamic calendar when Muslims fast from dawn to sunset.'
        ],
        [
            'question_text' => 'What is Zakat?',
            'option_a' => 'Fasting during Ramadan',
            'option_b' => 'Pilgrimage to Mecca',
            'option_c' => 'Charitable giving',
            'option_d' => 'Daily prayers',
            'correct_answer' => 'C',
            'explanation' => 'Zakat is the obligatory charitable giving, typically 2.5% of one\'s wealth annually.'
        ],
        [
            'question_text' => 'Who built the Kaaba?',
            'option_a' => 'Prophet Muhammad (PBUH)',
            'option_b' => 'Prophet Ibrahim (AS) and Ismail (AS)',
            'option_c' => 'Prophet Musa (AS)',
            'option_d' => 'Prophet Isa (AS)',
            'correct_answer' => 'B',
            'explanation' => 'The Kaaba was built by Prophet Ibrahim (Abraham) and his son Ismail (Ishmael) as the first house of worship for Allah.'
        ],
        [
            'question_text' => 'What is the meaning of "Islam"?',
            'option_a' => 'Peace',
            'option_b' => 'Submission to Allah',
            'option_c' => 'Love',
            'option_d' => 'Freedom',
            'correct_answer' => 'B',
            'explanation' => 'Islam means submission to the will of Allah and derives from the root word meaning peace.'
        ],
        [
            'question_text' => 'How many chapters (Surahs) are in the Quran?',
            'option_a' => '100',
            'option_b' => '110',
            'option_c' => '114',
            'option_d' => '120',
            'correct_answer' => 'C',
            'explanation' => 'The Quran contains 114 Surahs (chapters), ranging from the shortest to the longest.'
        ],
        [
            'question_text' => 'What is the Eid al-Fitr celebration?',
            'option_a' => 'Celebration of Hajj',
            'option_b' => 'Festival of breaking the fast after Ramadan',
            'option_c' => 'Birthday of Prophet Muhammad (PBUH)',
            'option_d' => 'Beginning of Islamic New Year',
            'correct_answer' => 'B',
            'explanation' => 'Eid al-Fitr is the festival celebrated at the end of Ramadan to mark the completion of fasting.'
        ],
        [
            'question_text' => 'What are the six articles of faith in Islam?',
            'option_a' => 'Five pillars only',
            'option_b' => 'Belief in Allah, angels, books, prophets, Day of Judgment, and divine decree',
            'option_c' => 'Belief in heaven and hell only',
            'option_d' => 'Belief in prayer and charity only',
            'correct_answer' => 'B',
            'explanation' => 'The six articles of faith are: belief in Allah, angels, revealed books, prophets, the Day of Judgment, and divine decree (Qadr).'
        ]
    ];
    
    $questionStmt = $pdo->prepare("
        INSERT INTO mcq_questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($questions as $q) {
        $questionStmt->execute([
            $testId,
            $q['question_text'],
            $q['option_a'],
            $q['option_b'],
            $q['option_c'],
            $q['option_d'],
            $q['correct_answer'],
            $q['explanation']
        ]);
    }
    
    $pdo->commit();
    
    echo "Demo MCQ test added successfully! Test ID: " . $testId . "<br>";
    echo "Added " . count($questions) . " questions.<br>";
    echo '<a href="mcq-tests.php">View MCQ Tests</a>';
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error adding demo MCQ test: " . $e->getMessage();
}

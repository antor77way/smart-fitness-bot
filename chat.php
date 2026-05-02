<?php

header('Content-Type: application/json');

$jsonFile = "data.json";

if(!file_exists($jsonFile))
{
    echo json_encode(['response'=>'Data file missing']);
    exit;
}

$jsonData = json_decode(file_get_contents($jsonFile), true);
$fitnessData = $jsonData['fitness_data'] ?? [];

$user=strtolower($_POST['message'] ?? '');

if(empty($user))
{
echo json_encode(['response'=>'Ask a fitness question']);
exit;
}



# BMI calculator

if(preg_match('/(\d+)\s*kg.*?(\d+)\s*cm|(\d+)\s*kg.*?(\d+)\s*m\s*$|weight\s*(\d+)\s*kg.*?height\s*(\d+)\s*cm/i',$user,$m))
{
    // Extract weight and height from whichever matched group
    $weight = $m[1] ?? $m[3] ?? $m[5] ?? null;
    $height_raw = $m[2] ?? $m[4] ?? $m[6] ?? null;
    
    if($weight && $height_raw) {
        // Convert height to meters if needed
        if($height_raw > 100) {
            $height = $height_raw / 100;
        } else {
            $height = $height_raw;
        }
        
        $bmi = $weight / ($height * $height);
        $bmi = round($bmi, 1);
        
        if($bmi < 18.5)
            $status = "Underweight";
        elseif($bmi < 25)
            $status = "Normal weight";
        elseif($bmi < 30)
            $status = "Overweight";
        else
            $status = "Obese";
        
        echo json_encode([
            'response' => "Your BMI is $bmi ($status)\n\nWeight: " . $weight . " kg\nHeight: " . $height_raw . " cm"
        ]);
        exit;
    }
}



# Word normalization (simple NLP)

$dictionary=[

'diet'=>['diet','food','eat','meal','nutrition','healthy food'],

'muscle'=>['muscle','gain','bulk','strength','body build'],

'weight'=>['weight','fat','loss','lose','obesity'],

'workout'=>['workout','exercise','training','practice'],

'equipment'=>['equipment','machine','tools','gym machine'],

'bmi'=>['bmi','body mass','fat level'],

'water'=>['water','drink','hydration'],

'beginner'=>['beginner','new','start','first time'],

'safety'=>['safety','injury','safe'],

'recovery'=>['recovery','rest','sleep'],

'supplement'=>['supplement','protein powder','creatine'],

'motivation'=>['motivation','lazy','discipline'],

'male'=>['male','men','man','boy'],

'female'=>['female','women','girl']

];



$detected=[];

foreach($dictionary as $topic=>$words)
{
    foreach($words as $word)
    {
        if(strpos($user,$word)!==false)
        {
            $detected[]=$topic;
            break;
        }
    }
}



# Male/Female Schedule Check
if(strpos($user, 'female') !== false && (strpos($user, 'schedule') !== false || strpos($user, 'time') !== false)) {
    $response = "**FEMALE GYM TIME SCHEDULE**\n\n";
    $response .= "Morning: " . $fitnessData['gym_schedule']['female']['morning']['time'] . "\n";
    $response .= "  → " . $fitnessData['gym_schedule']['female']['morning']['description'] . "\n\n";
    $response .= "Afternoon: " . $fitnessData['gym_schedule']['female']['afternoon']['time'] . "\n";
    $response .= "  → " . $fitnessData['gym_schedule']['female']['afternoon']['description'] . "\n";
    echo json_encode(['response' => $response]);
    exit;
}

if(strpos($user, 'male') !== false && strpos($user, 'female') === false && (strpos($user, 'schedule') !== false || strpos($user, 'time') !== false)) {
    $response = "**MALE GYM TIME SCHEDULE**\n\n";
    $response .= "Morning: " . $fitnessData['gym_schedule']['male']['morning']['time'] . "\n";
    $response .= "  → " . $fitnessData['gym_schedule']['male']['morning']['description'] . "\n\n";
    $response .= "Afternoon: " . $fitnessData['gym_schedule']['male']['afternoon']['time'] . "\n";
    $response .= "  → " . $fitnessData['gym_schedule']['male']['afternoon']['description'] . "\n\n";
    $response .= "Extra Training: " . $fitnessData['gym_schedule']['male']['extra_training']['time'] . "\n";
    $response .= "  → " . $fitnessData['gym_schedule']['male']['extra_training']['description'] . "\n";
    echo json_encode(['response' => $response]);
    exit;
}



foreach($detected as $topic)
{
    $response = "";
    
    if($topic === 'workout') {
        $response = "**WORKOUT BASICS**\n\n";
        $response .= "Guidelines:\n";
        foreach($fitnessData['workouts']['basics'] as $key => $value) {
            $response .= "• " . $value . "\n";
        }
        $response .= "\nBeginner Example:\n";
        foreach($fitnessData['workouts']['beginner_example'] as $exercise) {
            $response .= "• " . $exercise . "\n";
        }
        $response .= "\nRecommended Frequency:\n";
        foreach($fitnessData['workouts']['recommended_days'] as $level => $days) {
            $response .= "• " . ucfirst($level) . ": " . $days . "\n";
        }
    }
    elseif($topic === 'muscle') {
        $response = "**MUSCLE GAIN GUIDE**\n\n";
        $response .= "Requirements:\n";
        foreach($fitnessData['muscle_gain']['requirements'] as $req) {
            $response .= "• " . $req . "\n";
        }
        $response .= "\nBest Exercises:\n";
        foreach($fitnessData['muscle_gain']['best_exercises'] as $exercise => $target) {
            $response .= "• " . ucfirst(str_replace('_', ' ', $exercise)) . " → " . $target . "\n";
        }
        $response .= "\nTop Foods:\n";
        foreach(array_slice($fitnessData['muscle_gain']['foods'], 0, 5) as $food) {
            $response .= "• " . $food . "\n";
        }
        $response .= "\nTips:\n";
        foreach($fitnessData['muscle_gain']['tips'] as $tip) {
            $response .= "• " . $tip . "\n";
        }
    }
    elseif($topic === 'weight') {
        $response = "**WEIGHT LOSS GUIDE**\n\n";
        $response .= "Description: " . $fitnessData['weight_loss']['description'] . "\n\n";
        $response .= "Best Fat Burning Exercises:\n";
        foreach($fitnessData['weight_loss']['fat_burning_exercises'] as $exercise) {
            $response .= "• " . $exercise . "\n";
        }
        $response .= "\nTips:\n";
        foreach($fitnessData['weight_loss']['tips'] as $tip) {
            $response .= "• " . $tip . "\n";
        }
        $response .= "\nCardio Duration: " . $fitnessData['weight_loss']['cardio_time'] . "\n";
    }
    elseif($topic === 'diet') {
        $response = "**DIET PLAN**\n\n";
        $response .= $fitnessData['diet']['description'] . ":\n\n";
        $response .= "Protein Sources:\n";
        foreach($fitnessData['diet']['protein'] as $protein) {
            $response .= "• " . $protein . "\n";
        }
        $response .= "\nCarbohydrates:\n";
        foreach($fitnessData['diet']['carbohydrates'] as $carb) {
            $response .= "• " . $carb . "\n";
        }
        $response .= "\nVegetables:\n";
        foreach(array_slice($fitnessData['diet']['vegetables'], 0, 4) as $veg) {
            $response .= "• " . $veg . "\n";
        }
    }
    elseif($topic === 'water') {
        $response = "**HYDRATION GUIDE**\n\n";
        $response .= "Recommended: " . $fitnessData['hydration']['recommended'] . "\n\n";
        $response .= "Benefits:\n";
        foreach($fitnessData['hydration']['benefits'] as $benefit) {
            $response .= "• " . $benefit . "\n";
        }
    }
    elseif($topic === 'recovery') {
        $response = "**REST & RECOVERY**\n\n";
        $response .= $fitnessData['recovery']['description'] . "\n\n";
        $response .= "• Sleep: " . $fitnessData['recovery']['sleep'] . "\n";
        $response .= "• Rest Days: " . $fitnessData['recovery']['rest_days'] . "\n";
        $response .= "• Stretching: " . $fitnessData['recovery']['stretching'] . "\n";
        $response .= "• Nutrition: " . $fitnessData['recovery']['nutrition'] . "\n";
    }
    elseif($topic === 'bmi') {
        $response = "**BMI GUIDELINES**\n\n";
        foreach($fitnessData['bmi_guidelines'] as $category => $range) {
            $response .= "• " . ucfirst($category) . ": " . $range . "\n";
        }
    }
    elseif($topic === 'motivation') {
        $response = "**MOTIVATION TIPS**\n\n";
        foreach($fitnessData['motivation']['tips'] as $tip) {
            $response .= "• " . $tip . "\n";
        }
    }
    
    if(!empty($response)) {
        echo json_encode(['response' => $response]);
        exit;
    }
}



echo json_encode([
    'response' => "I can help with: Workout, Diet, BMI, Muscle Gain, Weight Loss, Hydration, Recovery, and Motivation. Ask me anything!"
]);

?>
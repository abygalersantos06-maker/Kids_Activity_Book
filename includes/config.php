<?php
// includes/config.php - Configuration and data

// Start session
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Product data (from database)
$products = array(
    // Coloring Books (6 items)
    array('id'=>1, 'title'=>'Jungle Coloring Fun', 'category'=>'Coloring Books', 'price'=>4.99, 'emoji'=>'🦁', 'age'=>'3-8', 'desc'=>'Jungle animals adventure'),
    array('id'=>2, 'title'=>'Underwater Adventure', 'category'=>'Coloring Books', 'price'=>5.50, 'emoji'=>'🐠', 'age'=>'4-9', 'desc'=>'Ocean coloring fun'),
    array('id'=>3, 'title'=>'Space Explorers', 'category'=>'Coloring Books', 'price'=>6.25, 'emoji'=>'🚀', 'age'=>'5-10', 'desc'=>'Color planets and rockets'),
    array('id'=>4, 'title'=>'Dinosaur World', 'category'=>'Coloring Books', 'price'=>4.95, 'emoji'=>'🦖', 'age'=>'3-8', 'desc'=>'Friendly dinosaurs'),
    array('id'=>5, 'title'=>'Fairy Tale Scenes', 'category'=>'Coloring Books', 'price'=>5.75, 'emoji'=>'🏰', 'age'=>'4-9', 'desc'=>'Castles and dragons'),
    array('id'=>6, 'title'=>'Animal Kingdom', 'category'=>'Coloring Books', 'price'=>4.50, 'emoji'=>'🐘', 'age'=>'2-7', 'desc'=>'Cute animals world'),
    
    // Puzzle Books (6 items)
    array('id'=>7, 'title'=>'Brain Teaser Puzzles', 'category'=>'Puzzle Books', 'price'=>6.99, 'emoji'=>'🧩', 'age'=>'5-10', 'desc'=>'Fun brain challenges'),
    array('id'=>8, 'title'=>'Number Games', 'category'=>'Puzzle Books', 'price'=>5.25, 'emoji'=>'🔢', 'age'=>'4-8', 'desc'=>'Math puzzles'),
    array('id'=>9, 'title'=>'Word Fun', 'category'=>'Puzzle Books', 'price'=>5.99, 'emoji'=>'📝', 'age'=>'5-9', 'desc'=>'Crosswords and word search'),
    array('id'=>10, 'title'=>'Logic Labyrinth', 'category'=>'Puzzle Books', 'price'=>6.50, 'emoji'=>'🌀', 'age'=>'4-8', 'desc'=>'Mazes and problem solving'),
    array('id'=>11, 'title'=>'Puzzle Planet', 'category'=>'Puzzle Books', 'price'=>5.95, 'emoji'=>'🌍', 'age'=>'3-7', 'desc'=>'Spot the difference'),
    array('id'=>12, 'title'=>'Riddle Me This', 'category'=>'Puzzle Books', 'price'=>4.99, 'emoji'=>'❓', 'age'=>'6-10', 'desc'=>'Fun riddles for kids'),
    
    // Educational Games (6 items)
    array('id'=>13, 'title'=>'Alphabet Adventure', 'category'=>'Educational Games', 'price'=>7.25, 'emoji'=>'🔤', 'age'=>'2-6', 'desc'=>'Learn letters'),
    array('id'=>14, 'title'=>'Counting Safari', 'category'=>'Educational Games', 'price'=>6.75, 'emoji'=>'🦒', 'age'=>'3-7', 'desc'=>'Numbers and counting'),
    array('id'=>15, 'title'=>'Shapes & Colors', 'category'=>'Educational Games', 'price'=>5.50, 'emoji'=>'⬛', 'age'=>'2-5', 'desc'=>'Learn shapes and colors'),
    array('id'=>16, 'title'=>'Animal Sounds', 'category'=>'Educational Games', 'price'=>6.25, 'emoji'=>'🐱', 'age'=>'1-4', 'desc'=>'Match animals to sounds'),
    array('id'=>17, 'title'=>'Memory Match', 'category'=>'Educational Games', 'price'=>5.99, 'emoji'=>'🎴', 'age'=>'3-8', 'desc'=>'Card memory game'),
    array('id'=>18, 'title'=>'Maze Fun', 'category'=>'Educational Games', 'price'=>5.25, 'emoji'=>'🔲', 'age'=>'4-8', 'desc'=>'Educational mazes'),
    
    // Printables (6 items)
    array('id'=>19, 'title'=>'Coloring Pages', 'category'=>'Printables', 'price'=>3.99, 'emoji'=>'📄', 'age'=>'All', 'desc'=>'PDF coloring pages'),
    array('id'=>20, 'title'=>'Worksheets', 'category'=>'Printables', 'price'=>4.25, 'emoji'=>'📃', 'age'=>'4-8', 'desc'=>'Learning worksheets'),
    array('id'=>21, 'title'=>'Craft Sheets', 'category'=>'Printables', 'price'=>4.50, 'emoji'=>'✂️', 'age'=>'5-10', 'desc'=>'Craft templates'),
    array('id'=>22, 'title'=>'Fun Mazes', 'category'=>'Printables', 'price'=>3.50, 'emoji'=>'〰️', 'age'=>'3-8', 'desc'=>'Printable mazes'),
    array('id'=>23, 'title'=>'Dot-to-Dot', 'category'=>'Printables', 'price'=>3.95, 'emoji'=>'⚫', 'age'=>'3-7', 'desc'=>'Connect the dots'),
    array('id'=>24, 'title'=>'Sticker Sheets', 'category'=>'Printables', 'price'=>4.75, 'emoji'=>'🏷️', 'age'=>'All', 'desc'=>'Printable stickers')
);

// Categories for navigation
$categories = array(
    'all' => 'All Books',
    'Coloring Books' => '🎨 Coloring',
    'Puzzle Books' => '🧩 Puzzles',
    'Educational Games' => '📚 Educational',
    'Printables' => '📄 Printables'
);

// Reviews data
$reviews = array(
    array('name'=>'Emma (age 7)', 'rating'=>5, 'text'=>'I love the dinosaur book! Colors are so pretty.'),
    array('name'=>'Liam\'s mom', 'rating'=>5, 'text'=>'My son spends hours with the mazes. Great quality!'),
    array('name'=>'Teacher Ava', 'rating'=>4, 'text'=>'Perfect for classroom. Kids love the alphabet book.'),
    array('name'=>'Noah (age 5)', 'rating'=>5, 'text'=>'Counting safari is my favorite!')
);
?>
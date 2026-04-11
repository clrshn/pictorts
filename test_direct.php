<?php
echo 'Testing updateStatus method directly...' . PHP_EOL;
$todo = App\Models\Todo::find(1);
if ($todo) {
    echo 'Todo found: ' . $todo->title . PHP_EOL;
    $todo->update(['status' => 'done']);
    echo 'Status updated successfully' . PHP_EOL;
} else {
    echo 'Todo not found' . PHP_EOL;
}

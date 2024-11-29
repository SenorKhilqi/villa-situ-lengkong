<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Update payment status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $booking_id = intval($_POST['booking_id']);
        $new_status = 'completed';
        $conn->query("UPDATE bookings SET payment_status = '$new_status' WHERE id = $booking_id");
        header("Location: admin_dashboard.php");
        exit();
    } elseif (isset($_POST['delete_booking'])) {
        $booking_id = intval($_POST['booking_id']);
        $conn->query("DELETE FROM bookings WHERE id = $booking_id");
        header("Location: admin_dashboard.php");
        exit();
    }
}

$result = $conn->query("
    SELECT bookings.id AS booking_id, users.username, villas.name AS villa_name, villas.price, bookings.booking_date, bookings.payment_status, bookings.payment_method
    FROM bookings 
    JOIN users ON bookings.user_id = users.id 
    JOIN villas ON bookings.villa_id = villas.id 
    ORDER BY bookings.booking_date
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

<div class="container mx-auto mt-10 ">
    <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Daftar Booking</h2>
    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-green-500 text-white">
                    <th class="py-3 px-4 text-left">Username</th>
                    <th class="py-3 px-4 text-left">Villa</th>
                    <th class="py-3 px-4 text-left">Tanggal Booking</th>
                    <th class="py-3 px-4 text-left">Harga</th>
                    <th class="py-3 px-4 text-left">Metode Pembayaran</th>
                    <th class="py-3 px-4 text-left">Status Pembayaran</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['villa_name']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td class="py-3 px-4 text-left text-green-500 font-bold">Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td class="py-3 px-4 font-bold <?php echo strtolower($row['payment_status']) === 'pending' ? 'text-yellow-500' : (strtolower($row['payment_status']) === 'completed' ? 'text-green-500' : 'text-red-500'); ?>">
                            <?php echo htmlspecialchars($row['payment_status']); ?>
                        </td>
                        <td class="py-3 px-4 space-x-2">
                            <?php if (strtolower($row['payment_status']) === 'pending') { ?>
                                <form method="POST" action="" class="inline">
                                    <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                    <button type="submit" name="update_status" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Tandai Lunas
                                    </button>
                                </form>
                            <?php } else { ?>
                                <span class="text-gray-500">Lunas</span>
                            <?php } ?>
                            <form method="POST" action="" class="inline">
                                <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                                <button type="submit" name="delete_booking" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-center">
        <a href="logout.php" class="text-blue-500 hover:underline">Logout</a>
    </div>
</div>

</body>
</html>

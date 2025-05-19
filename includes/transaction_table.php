<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['title']); ?></td>
                <td><?php echo htmlspecialchars($transaction['author']); ?></td>
                <td><?php echo formatDate($transaction['borrow_date']); ?></td>
                <td><?php echo formatDate($transaction['due_date']); ?></td>
                <td>
                    <?php echo $transaction['return_date'] ? formatDate($transaction['return_date']) : '-'; ?>
                </td>
                <td>
                    <?php if ($transaction['status'] === 'borrowed'): ?>
                    <span class="status-borrowed">Borrowed</span>
                    <?php else: ?>
                    <span class="status-available">Returned</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
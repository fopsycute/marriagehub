<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarriageHub Database Migration Runner</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .warning h3 {
            color: #856404;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .warning p {
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .migration-list {
            margin-bottom: 30px;
        }
        
        .migration-item {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .migration-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        
        .migration-item.success {
            border-color: #28a745;
            background: #f0fff4;
        }
        
        .migration-item.error {
            border-color: #dc3545;
            background: #fff5f5;
        }
        
        .migration-item.running {
            border-color: #ffc107;
            background: #fffbf0;
        }
        
        .migration-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .migration-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .migration-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-pending {
            background: #e0e0e0;
            color: #666;
        }
        
        .badge-running {
            background: #ffc107;
            color: #fff;
        }
        
        .badge-success {
            background: #28a745;
            color: #fff;
        }
        
        .badge-error {
            background: #dc3545;
            color: #fff;
        }
        
        .migration-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .migration-result {
            margin-top: 15px;
            padding: 12px;
            background: white;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            display: none;
        }
        
        .migration-result.show {
            display: block;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #e0e0e0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 MarriageHub Database Migrations</h1>
            <p>Phase 2 & Phase 3 - Run all database migrations with one click</p>
        </div>
        
        <div class="content">
            <div class="warning">
                <h3>⚠️ Important Notice</h3>
                <p><strong>Backup your database before running migrations!</strong> These operations will modify your database structure. Make sure you have a recent backup in case you need to rollback changes.</p>
            </div>
            
            <div class="progress-bar">
                <div class="progress-fill" id="progressBar"></div>
            </div>
            
            <div class="migration-list" id="migrationList">
                <!-- Migrations will be loaded here -->
            </div>
            
            <div class="btn-group">
                <button class="btn btn-primary" id="runAllBtn" onclick="runAllMigrations()">
                    Run All Migrations
                </button>
                <button class="btn btn-secondary" onclick="location.reload()">
                    Reset
                </button>
            </div>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number" id="totalCount">0</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="successCount" style="color: #28a745;">0</div>
                    <div class="stat-label">Success</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="errorCount" style="color: #dc3545;">0</div>
                    <div class="stat-label">Errors</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const migrations = [
            {
                id: 1,
                title: 'Feedback System Table',
                file: 'database/create_feedback.php',
                description: 'Creates ma_feedback table for content helpfulness voting (yes/no)'
            },
            {
                id: 2,
                title: 'View Tracking Tables',
                file: 'database/create_view_tracking.php',
                description: 'Creates ma_blog_views and ma_question_views for unique view tracking'
            },
            {
                id: 3,
                title: 'Therapist Unavailable Dates',
                file: 'database/create_therapist_unavailable.php',
                description: 'Creates ma_therapist_unavailable table for calendar management'
            },
            {
                id: 4,
                title: 'Dispute Resolution Fields',
                file: 'database/add_dispute_resolution.php',
                description: 'Adds resolution tracking columns to ma_disputes table'
            },
            {
                id: 5,
                title: 'Order Tracking Fields',
                file: 'database/add_order_tracking.php',
                description: 'Adds updated_at and vendor_notes columns to ma_orders table'
            },
            {
                id: 6,
                title: 'Promotional Codes System',
                file: 'database/create_promo_codes.php',
                description: 'Creates ma_promo_codes and ma_promo_usage tables for discount system'
            }
        ];

        let stats = { total: migrations.length, success: 0, error: 0 };

        function initMigrations() {
            const listEl = document.getElementById('migrationList');
            document.getElementById('totalCount').textContent = stats.total;
            
            migrations.forEach(migration => {
                const itemEl = document.createElement('div');
                itemEl.className = 'migration-item';
                itemEl.id = `migration-${migration.id}`;
                itemEl.innerHTML = `
                    <div class="migration-header">
                        <div class="migration-title">${migration.id}. ${migration.title}</div>
                        <span class="migration-badge badge-pending" id="badge-${migration.id}">Pending</span>
                    </div>
                    <div class="migration-description">${migration.description}</div>
                    <div class="migration-result" id="result-${migration.id}"></div>
                `;
                listEl.appendChild(itemEl);
            });
        }

        async function runMigration(migration) {
            const itemEl = document.getElementById(`migration-${migration.id}`);
            const badgeEl = document.getElementById(`badge-${migration.id}`);
            const resultEl = document.getElementById(`result-${migration.id}`);
            
            // Set running state
            itemEl.className = 'migration-item running';
            badgeEl.className = 'migration-badge badge-running';
            badgeEl.innerHTML = '<span class="spinner"></span>Running...';
            
            try {
                const response = await fetch(migration.file);
                const text = await response.text();
                
                // Check if response contains error indicators
                const hasError = text.includes('❌') || text.includes('Error') || text.includes('Failed');
                
                if (hasError) {
                    // Error state
                    itemEl.className = 'migration-item error';
                    badgeEl.className = 'migration-badge badge-error';
                    badgeEl.textContent = 'Error';
                    stats.error++;
                } else {
                    // Success state
                    itemEl.className = 'migration-item success';
                    badgeEl.className = 'migration-badge badge-success';
                    badgeEl.textContent = 'Success';
                    stats.success++;
                }
                
                // Show result
                resultEl.innerHTML = text;
                resultEl.classList.add('show');
                
            } catch (error) {
                // Network or fetch error
                itemEl.className = 'migration-item error';
                badgeEl.className = 'migration-badge badge-error';
                badgeEl.textContent = 'Error';
                resultEl.innerHTML = `<span style="color: #dc3545;">❌ Error: ${error.message}</span>`;
                resultEl.classList.add('show');
                stats.error++;
            }
            
            // Update stats
            document.getElementById('successCount').textContent = stats.success;
            document.getElementById('errorCount').textContent = stats.error;
        }

        async function runAllMigrations() {
            const btn = document.getElementById('runAllBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span>Running Migrations...';
            
            stats.success = 0;
            stats.error = 0;
            
            for (let i = 0; i < migrations.length; i++) {
                await runMigration(migrations[i]);
                
                // Update progress bar
                const progress = ((i + 1) / migrations.length) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                
                // Small delay between migrations
                await new Promise(resolve => setTimeout(resolve, 500));
            }
            
            // All done
            btn.disabled = false;
            if (stats.error === 0) {
                btn.className = 'btn btn-success';
                btn.textContent = '✓ All Migrations Completed Successfully!';
            } else {
                btn.className = 'btn btn-primary';
                btn.textContent = 'Run All Migrations';
                alert(`Completed with ${stats.error} error(s). Check the results above.`);
            }
        }

        // Initialize on page load
        window.onload = initMigrations;
    </script>
</body>
</html>

<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Semua Produk - Kampus Susu Bergoyang</title>
    <style>
        :root {
                --primary: #2E8B57;
                --secondary: #3CB371; 
                --accent: #90EE90; 
                --light: #F0FFF0; 
                --dark: #006400; 
                --text: #2F4F4F; 
                --success: #4CAF50;
            }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--light);
            color: var(--text);
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .cow-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23ffffff' d='M18.5,15C19.9,15 21,13.9 21,12.5C21,11.1 19.9,10 18.5,10H16.91C17.59,9.26 18,8.31 18,7.26C18,5.6 16.66,4.26 15,4.26C14.22,4.26 13.5,4.53 12.91,5L12,5.76L11.09,5C10.5,4.53 9.78,4.26 9,4.26C7.34,4.26 6,5.6 6,7.26C6,8.31 6.41,9.26 7.09,10H5.5C4.1,10 3,11.1 3,12.5C3,13.9 4.1,15 5.5,15H7.05C7,15.16 7,15.33 7,15.5C7,17.71 8.79,19.5 11,19.5C12.15,19.5 13.18,19 14,18.23C14.82,19 15.85,19.5 17,19.5C19.21,19.5 21,17.71 21,15.5C21,15.33 21,15.16 20.95,15H18.5M5.5,12C5.22,12 5,12.22 5,12.5C5,12.78 5.22,13 5.5,13H11V12H5.5M18.5,13C18.78,13 19,12.78 19,12.5C19,12.22 18.78,12 18.5,12H13V13H18.5Z'/%3E%3C/svg%3E");
            background-repeat: repeat;
            z-index: 0;
        }
        
        .logo {
            position: relative;
            z-index: 1;
        }
        
        .logo h1 {
            font-size: 2.5rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 10px;
            letter-spacing: 2px;
        }
        
        .logo p {
            color: var(--accent);
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            overflow: hidden; /* Ini penting untuk potong gambar yang lebih besar */
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Biar gambar memenuhi kotak dengan proporsi pas */
            display: block;
        }

        
        .product-info {
            padding: 20px;
        }
        
        .product-info h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .product-info .price {
            font-weight: bold;
            color: var(--dark);
            font-size: 1.2rem;
            margin: 10px 0;
        }
        
        .product-info .description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }
        
        .btn:hover {
            background-color: var(--dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            color: var(--primary);
            z-index: 100;
        }
        
        .back-button:hover {
            color: var(--dark);
        }

        
        @media (max-width: 768px) {
            .about-content {
                flex-direction: column;
            }
            
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            
            nav ul li {
                margin: 5px 0;
            }

            html {
                scroll-behavior: smooth;
            }

        }
    </style>
</head>
<body>

 <!-- Back Button -->
<a href="tes.php" class="back-button" style="
    position: absolute;
    top: 90px; /* dinaikkan dari 20px agar tidak ketutupan header */
    left: 20px;
    font-size: 1.8rem;   
    color:rgb(255, 255, 255);
    z-index: 999;
    text-decoration: none;
    padding: 8px 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
">
    <i class="fas fa-arrow-left"></i>
</a>
     <!-- Header -->
    <header>
        <div class="cow-bg"></div>
        <div class="container logo">
            <h1>SELAMAT DATANG DI KAMPUS SUSU BERGOYANG</h1>
            <p>Kampung Susu Bergoyang menyediakan layanan reservasi joglo sebagai sarana edukasi dan menjual berbagai produk hasil olahan susu sapi.</p>
        </div>
    </header>
    
   <section id="products" style="background-color: #FFF8DC; padding: 50px 0;">
       <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-title" style="text-align: center; margin-bottom: 40px;">
                 <h2 style="color: #2E8B57; font-size: 32px;">Semua Produk Kami</h2>
            </div>

             <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
                <?php
                $sql = "SELECT * FROM products";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
                            <div class="product-image" style="height: 200px; display: flex; justify-content: center; align-items: center; background-color: #F5DEB3;">
                                <img src="uploads/'.$row['image'].'" alt="'.$row['name'].'" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            <div class="product-info" style="padding: 20px;">
                                <h3 style="margin: 0 0 10px; color: #2E8B57; font-size: 20px;">'.$row['name'].'</h3>
                                <div class="price" style="font-weight: bold; color: #2E8B57; margin-bottom: 10px;">Rp '.number_format($row['price'],0,',','.').'</div>
                                <div class="description" style="color: #666; margin-bottom: 15px; height: 60px; overflow: hidden;">'.$row['description'].'</div>
                                <a href="https://wa.me/62895390892346?text=Halo%20saya%20ingin%20membeli%20produk%20'.$row['name'].'%20dari%20Kampus%20Susu%20Bergoyang" class="btn" target="_blank" style="display: inline-block; background-color: #2E8B57; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; text-align: center; width: 100%;">Beli Sekarang</a>
                            </div>
                        </div>';
                    }
                } else {
                    echo "<p style='text-align: center; grid-column: 1 / -1;'>Tidak ada produk tersedia</p>";
                }
                ?>
            </div>
        </div>
    </section>  

</body>
</html>
<?php
// Enable error reporting for development - REMOVE IN PRODUCTION
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php'; // Include your database configuration. Ensure this file connects to $conn.

$products = []; // Initialize products array

// Fetch products from the database using a prepared statement for security
// CORRECTED: Changed 'image_path' to 'imagem' as per your database schema
$sql_products = "SELECT id, name, description, price, imagem FROM productsz ORDER BY id DESC LIMIT 3"; // Limit to 3 for featured
$stmt_products = $conn->prepare($sql_products);

if ($stmt_products) {
    $stmt_products->execute();
    $result_products = $stmt_products->get_result();

    if ($result_products && $result_products->num_rows > 0) {
        while ($row = $result_products->fetch_assoc()) {
            $products[] = $row;
        }
    }
    $stmt_products->close();
} else {
    // Log error if prepared statement fails
    error_log("Failed to prepare product query: " . $conn->error);
}

// Close the database connection
// IMPORTANT: Only close the connection IF 'config.php' doesn't intend to keep it open
// for other parts of the script. If this is the *only* DB interaction on this page, it's fine.
// If other included files/scripts on this page also use $conn, remove this line.
if ($conn) {
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
    />
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>AgriApp - Conectando o Campo à Sua Mesa</title>
</head>
<body>
    <nav>
        <div class="nav__header">
            <div class="logo nav__logo">
                <a href="#">Agri<span>App</span></a>
            </div>
            <div class="nav__menu__btn" id="menu-btn">
                <span><i class="ri-menu-line"></i></span>
            </div>
        </div>
        <ul class="nav__links" id="nav-links">
<li><a href="#" id="inicio-link">Início</a></li>
            <li><a href="#special">Produtos</a></li> <li><a href="#chef">Agricultores</a></li>
            <li><a href="#client">Depoimentos</a></li>
        </ul>
        <div class="nav__btn">
            <button class="btn" onclick="window.location.href='login.php';">
                <i class="ri-user-fill"></i> Entrar
            </button>
        </div>
    </nav>

    <header class="section__container header__container" id="home">
        <div class="header__image">
            <img src="agri 2.jpg" alt="Cesta de Legumes Frescos" />
        </div>
        <div class="header__content">
            <h1>Conectando Agricultores & Consumidores <span>com Simplicidade</span>.</h1>
            <p class="section__description">
                Descubra a verdadeira essência da agricultura sustentável. Conectamos
                agricultores locais a consumidores conscientes, promovendo uma cadeia
                alimentar mais justa, fresca e saudável.
            </p>
            <div class="header__btn">
                <a href="login.php"><button class="btn">Comece Agora</button></a>
            </div>
        </div>
    </header>

    <section class="section__container special__container" id="special">
        <h2 class="section__header">Nossos Produtos em Destaque</h2>
        <p class="section__description">
            Cada produto é cultivado com cuidado e respeito ao meio ambiente,
            garantindo qualidade e sabor incomparáveis.
        </p>
        <div class="special__grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="special__card">
                        <?php
                            // Determine the correct image path with fallback
                            $product_image_src = 'cliente/assets/default_product.png'; // Fallback default image
                            // Prioritize 'agricultor/uploads/' as per your clarification
                            if (!empty($product['imagem']) && file_exists('agricultor/uploads/' . $product['imagem'])) {
                                $product_image_src = 'agricultor/uploads/' . htmlspecialchars($product['imagem']);
                            } elseif (!empty($product['imagem']) && file_exists('cliente/assets/' . $product['imagem'])) {
                                // Secondary fallback: Check if images are in 'cliente/assets'
                                $product_image_src = 'cliente/assets/' . htmlspecialchars($product['imagem']);
                            }
                        ?>
                        <img src="<?= $product_image_src ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                        <h4><?= htmlspecialchars($product['name']) ?></h4>
                        <p><?= htmlspecialchars($product['description']) ?></p>
                    
                            
                        <div class="special__footer">
                            <p class="price">Kz <?= number_format($product['price'], 2, ',', '.') ?></p>
                            <button class="btn" onclick="window.location.href='login.php';">Adicionar ao Carrinho</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="special__card">
                    <img src="cliente/assets/tomate.png" alt="Tomate Orgânico" />
                    <h4>Tomate Orgânico</h4>
                    <p>
                        Cultivado sem agrotóxicos, nosso tomate é suculento e cheio de sabor.
                    </p>
                    <div class="special__ratings">
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                    </div>
                    <div class="special__footer">
                        <p class="price">Kz 12,50</p>
                        <button class="btn" onclick="window.location.href='login.php';">Adicionar ao Carrinho</button>
                    </div>
                </div>
                <div class="special__card">
                    <img src="cliente/assets/batatadoce.png" alt="Batata Doce" />
                    <h4>Batata Doce</h4>
                    <p>
                        Rica em nutrientes, nossa batata doce é perfeita para uma alimentação
                        saudável.
                    </p>
                    <div class="special__ratings">
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                    </div>
                    <div class="special__footer">
                        <p class="price">Kz 18,50</p>
                        <button class="btn" onclick="window.location.href='login.php';">Adicionar ao Carrinho</button>
                    </div>
                </div>
                <div class="special__card">
                    <img src="cliente/assets/maçavermelha.webp" alt="Maçãs Vermelhas" />
                    <h4>Maçãs Vermelhas</h4>
                    <p>
                        Maçãs vermelhas suculentas, colhidas diretamente do pomar.
                    </p>
                    <div class="special__ratings">
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                        <span><i class="ri-star-fill"></i></span>
                    </div>
                    <div class="special__footer">
                        <p class="price">Kz 15,50</p>
                        <button class="btn" onclick="window.location.href='login.php';">Adicionar ao Carrinho</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section__container explore__container">
        <div class="explore__image">
            <img src="agri 1.jpg" alt="Agricultura Sustentável" />
        </div>
        <div class="explore__content">
            <h1 class="section__header">Agricultura Sustentável e Saudável</h1>
            <p class="section__description">
                Promovemos práticas agrícolas que respeitam o meio ambiente e garantem
                alimentos saudáveis e nutritivos para todos. Nossa paixão é levar o melhor
                do campo diretamente para a sua mesa.
            </p>
            <div class="explore__btn">
                <button class="btn" onclick="window.location.href='login.php';">
                    Saiba Mais <span><i class="ri-arrow-right-line"></i></span>
                </button>
            </div>
        </div>
    </section>

    <section class="section__container banner__container">
        <div class="banner__card">
            <span class="banner__icon"><i class="ri-leaf-fill"></i></span>
            <h4>Agricultura Orgânica</h4>
            <p>
                Cultivamos alimentos sem agrotóxicos, garantindo qualidade e saúde para
                você e sua família.
            </p>
            <a href="login.php">
                Saiba mais
                <span><i class="ri-arrow-right-line"></i></span>
            </a>
        </div>
        
        <div class="banner__card">
            <span class="banner__icon"><i class="ri-heart-fill"></i></span>
            <h4>Consumo Consciente</h4>
            <p>
                Apoiamos práticas de consumo consciente, promovendo uma cadeia
                alimentar mais justa, sustentável e responsável.
            </p>
            <a href="login.php">
                Saiba mais
                <span><i class="ri-arrow-right-line"></i></span>
            </a>
        </div>
    </section>

    <section class="chef" id="chef">
        <img src="cliente/assets/planta.png" alt="Agricultores" class="chef__bg" />
        <div class="section__container chef__container">
            <div class="chef__image">
                <img src="cliente/assets/agricultor.png" alt="Agricultor" />
            </div>
            <div class="chef__content">
                <h2 class="section__header">Conheça Nossos Agricultores</h2>
                <p class="section__description">
                    Nossos agricultores são a alma da AgriApp, especialistas em práticas sustentáveis,
                    garantindo alimentos de alta qualidade e respeito inabalável ao meio ambiente.
                </p>
                <ul class="chef__list">
                    <li>
                        <span><i class="ri-checkbox-fill"></i></span>
                        Cultivo orgânico e sustentável.
                    </li>
                    <li>
                        <span><i class="ri-checkbox-fill"></i></span>
                        Respeito ao meio ambiente e aos recursos naturais.
                    </li>
                    <li>
                        <span><i class="ri-checkbox-fill"></i></span>
                        Compromisso com a qualidade, frescor e a saúde dos consumidores.
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section class="section__container client__container" id="client">
        <h2 class="section__header">O Que Nossos Clientes Dizem</h2>
        <p class="section__description">
            Veja depoimentos reais de quem já experimentou e se apaixonou pelos nossos produtos e serviços.
        </p>
        <div class="client__swiper">
  <div class="swiper">
    <div class="swiper-wrapper">
      
      <!-- Consumidora -->
      <div class="swiper-slide">
        <div class="client__card">
          <p>
            Antes, era difícil encontrar produtos agrícolas frescos e confiáveis. Com a AgriApp, posso comprar diretamente dos produtores, com rapidez, qualidade e total transparência. Facilitou muito minha rotina!
          </p>
          <img src="cliente\assets\sandra.jpeg" alt="Cliente Consumidora" />
          <h4>Sandra Batalha</h4>
          <h5>Consumidora</h5>
        </div>
      </div>

      <!-- Agricultor -->
      <div class="swiper-slide">
        <div class="client__card">
          <p>
            Eu cultivo há muito tempo, mas enfrentava dificuldades para vender. A AgriApp deu visibilidade à minha produção. Hoje tenho clientes fixos e consigo negociar com melhores preços, direto com os compradores.
          </p>
          <img src="cliente\assets\Alfeo.webp" alt="Cliente Agricultor" />
          <h4>Alfeo Vinevala</h4>
          <h5>Agricultor</h5>
        </div>
      </div>

      <!-- Especialista no comércio -->
      <div class="swiper-slide">
        <div class="client__card">
          <p>
            A AgriApp representa um avanço importante no setor agrícola angolano. Reduz a informalidade, conecta oferta e demanda, e promove um comércio mais eficiente e justo para todos os envolvidos.
          </p>
          <img src="cliente\assets\manuel.jpeg" alt="Especialista Manuel Cafala" />
          <h4>Manuel Lemos</h4>
          <h5>Especialista Comercial</h5>
        </div>
      </div>

    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>

    </section>

   <footer class="footer" id="contact">
  <div class="section__container footer__container">
    
    <!-- Logo + descrição -->
    <div class="footer__col">
      <div class="footer__logo">
        <a href="#">Agri<span>app</span></a>
      </div>
      <p class="footer__description">
        Conectamos agricultores e consumidores em uma plataforma mais justa,
        transparente e sustentável para um futuro melhor.
      </p>
    </div>

    <!-- Produtos -->
    <div class="footer__col">
      <h4>Produtos</h4>
      <ul class="footer__links">
        <li><a href="#special">Hortaliças</a></li>
        <li><a href="#special">Frutas</a></li>
        <li><a href="#special">Grãos</a></li>
        <li><a href="#special">Orgânicos</a></li>
      </ul>
    </div>

    <!-- Informações -->
    <div class="footer__col">
      <h4>Informações</h4>
      <ul class="footer__links">
        <li><a href="#">Sobre Nós</a></li>
        <li><a href="#contact">Contacto: 936008874 / 949086521</a></li>
        <li><a href="#">Política de Privacidade</a></li>
      </ul>
    </div>

    <!-- Redes Sociais -->
    <div class="footer__col">
      <h4>Redes Sociais</h4>
      <ul class="footer__links">
        <li><a href="#"><i class="ri-facebook-fill"></i> Facebook</a></li>
        <li><a href="#"><i class="ri-instagram-line"></i> Instagram</a></li>
        <li><a href="#"><i class="ri-twitter-fill"></i> Twitter</a></li>
      </ul>
    </div>

  </div>

  <div class="footer__bar">
    Copyright © 2025 AgriApp. Todos os direitos reservados.
  </div>
</footer>


    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const menuBtn = document.getElementById("menu-btn");
        const navLinks = document.getElementById("nav-links");
        const menuBtnIcon = menuBtn.querySelector("i");

        menuBtn.addEventListener("click", (e) => {
            navLinks.classList.toggle("open");
            const isOpen = navLinks.classList.contains("open");
            menuBtnIcon.setAttribute("class", isOpen ? "ri-close-line" : "ri-menu-line");
        });

        navLinks.addEventListener("click", (e) => {
            if (e.target.tagName === 'A') { // Only close if an actual link is clicked
                navLinks.classList.remove("open");
                menuBtnIcon.setAttribute("class", "ri-menu-line");
            }
        });

        const scrollRevealOption = {
            distance: "50px",
            origin: "bottom",
            duration: 1000,
        };

        ScrollReveal().reveal(".header__image img", {
            ...scrollRevealOption,
            origin: "right",
        });
        ScrollReveal().reveal(".header__content h1", {
            ...scrollRevealOption,
            delay: 500,
        });
        ScrollReveal().reveal(".header__content .section__description", {
            ...scrollRevealOption,
            delay: 1000,
        });
        ScrollReveal().reveal(".header__content .header__btn", {
            ...scrollRevealOption,
            delay: 1500,
        });

        ScrollReveal().reveal(".explore__image img", {
            ...scrollRevealOption,
            origin: "left",
        });
        ScrollReveal().reveal(".explore__content .section__header", {
            ...scrollRevealOption,
            delay: 500,
        });
        ScrollReveal().reveal(".explore__content .section__description", {
            ...scrollRevealOption,
            delay: 1000,
        });
        ScrollReveal().reveal(".explore__content .explore__btn", {
            ...scrollRevealOption,
            delay: 1500,
        });

        ScrollReveal().reveal(".banner__card", {
            ...scrollRevealOption,
            interval: 300, // Reduced interval for slightly faster animation
        });

        ScrollReveal().reveal(".chef__image img", {
            ...scrollRevealOption,
            origin: "right",
        });
        ScrollReveal().reveal(".chef__content .section__header", {
            ...scrollRevealOption,
            delay: 500,
        });
        ScrollReveal().reveal(".chef__content .section__description", {
            ...scrollRevealOption,
            delay: 1000,
        });
        ScrollReveal().reveal(".chef__list li", {
            ...scrollRevealOption,
            delay: 1500,
            interval: 200, // Reduced interval for faster list animation
        });

        const swiper = new Swiper(".swiper", {
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true, // Make pagination bullets clickable
            },
            autoplay: { // Added autoplay for testimonials
                delay: 8000,
                disableOnInteraction: false,
            },
            navigation: { // Optionally add navigation arrows if desired
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
        document.getElementById("inicio-link").addEventListener("click", function(e) {
    e.preventDefault();
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});

    </script>
</body>
</html>
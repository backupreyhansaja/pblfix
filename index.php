<?php
require_once 'config/database.php';
$pageTitle = 'Beranda';

// Fetch data
$db = new Database();

// Get Visi Misi from content_dashboard
$visiMisiResult = $db->query("SELECT * FROM content_dashboard WHERE type = 'visi_misi' LIMIT 1");
$visiMisiRow = $db->fetch($visiMisiResult);
$visiMisi = [];
if ($visiMisiRow && !empty($visiMisiRow['data'])) {
    $jsonData = json_decode($visiMisiRow['data'], true);
    if ($jsonData) {
        $visiMisi = $jsonData;
    }
}

// Get Sejarah from content_dashboard
$sejarahResult = $db->query("SELECT * FROM content_dashboard WHERE type = 'sejarah' LIMIT 1");
$sejarahRow = $db->fetch($sejarahResult);
$sejarah = [];
if ($sejarahRow && !empty($sejarahRow['data'])) {
    $jsonData = json_decode($sejarahRow['data'], true);
    if ($jsonData) {
        $sejarah = ['content' => $jsonData['content']];
    }
}

// Get Struktur Organisasi with Dosen data and Files
$strukturResult = $db->query("
    SELECT 
        so.*, 
        d.nama, 
        d.nip,
        (f.path || '/' || f.filename) AS foto_path
    FROM struktur_organisasi so
    LEFT JOIN dosen d ON so.id_dosen = d.id
    LEFT JOIN files f ON so.foto_id = f.id
    ORDER BY so.urutan ASC
");
$struktur = $db->fetchAll($strukturResult);

// Get News/Announcements
$newsResult = $db->query("
    SELECT 
        b.*,
        (f.path || '/' || f.filename) AS gambar_path
    FROM berita b
    LEFT JOIN files f ON b.image_id = f.id
    ORDER BY b.tanggal DESC, b.created_at DESC 
    LIMIT 6
");
$news = $db->fetchAll($newsResult);

// Get Gallery
$galleryResult = $db->query("
    SELECT 
        g.*,
        (f.path || '/' || f.filename) AS image_path
    FROM gallery g
    LEFT JOIN files f ON g.image_id = f.id
    ORDER BY g.created_at DESC 
    LIMIT 9
");
$gallery = $db->fetchAll($galleryResult);

//Get Scope
$scopeResult = $db->query("SELECT * FROM scope ORDER BY urutan ASC, id ASC");
$scope = $db->fetchAll($scopeResult);

//Get Mission (optional - table may not exist yet)
$mission = [];
// Note: mission table not in current schema, using empty array for now

//Get Priority Research Topics (optional - table may not exist yet)
$researchTopics = [];
// Note: priority_research_topics table not in current schema, using empty array for now


include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);">
    <!-- Animated Background Blobs -->
    <div class="absolute top-20 left-20 w-72 h-72 blob"></div>
    <div class="absolute bottom-20 right-20 w-96 h-96 blob" style="animation-delay: 2s;"></div>
    
    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center text-white" data-aos="fade-up">
            <div class="mb-6 animate-float">
                <i class="fas fa-flask text-8xl opacity-90"></i>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                Selamat Datang di<br>
                <span class="text-blue-200">Laboratorium Kampus</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-100 max-w-3xl mx-auto">
                Pusat Inovasi dan Penelitian Teknologi Informasi
            </p>
            <div class="flex justify-center space-x-4">
                <a href="#scope" class="px-8 py-4 bg-white text-blue-900 rounded-lg font-semibold hover-scale shadow-lg">
                    Explore Now
                </a>
                <a href="#contact" class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-blue-900 transition">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
        <i class="fas fa-chevron-down text-white text-3xl"></i>
    </div>
</section>

<!-- SCOPE Section -->
<section id="scope" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800">SCOPE</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Lingkup Layanan dan Kegiatan Laboratorium</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (!empty($scope)): ?>
                <?php foreach ($scope as $item): ?>
                    <?php 
                    $scopeTitle = !empty($item['title']) ? $item['title'] : (!empty($item['judul']) ? $item['judul'] : '');
                    $scopeDesc = !empty($item['description']) ? $item['description'] : (!empty($item['deskripsi']) ? $item['deskripsi'] : '');
                    ?>
                    <div class="card-hover bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600" data-aos="fade-up" data-aos-delay="100">
                        <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <?php if (!empty($item['icon'])): ?>
                                <i class="<?php echo htmlspecialchars($item['icon']); ?> text-3xl text-blue-600"></i>
                            <?php else: ?>
                                <i class="fas fa-cog text-3xl text-blue-600"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-800"><?php echo htmlspecialchars($scopeTitle); ?></h3>
                        <p class="text-gray-600 text-sm leading-relaxed"><?php echo htmlspecialchars($scopeDesc); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default Scope Items -->
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600" data-aos="fade-up">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-laptop-code text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Information System & Automation</h3>
                    <p class="text-gray-600 text-sm">Building information systems to support organizational management, business, health, and education.</p>
                </div>
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-brain text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Artificial Intelligence</h3>
                    <p class="text-gray-600 text-sm">Analyze data, create machine learning models, and develop intelligent systems that can assist decision-making.</p>
                </div>
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-mobile-alt text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Application Development</h3>
                    <p class="text-gray-600 text-sm">Designing and building desktop, web, and mobile applications for industrial and academic needs.</p>
                </div>
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-network-wired text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">IoT & Applied Technologies</h3>
                    <p class="text-gray-600 text-sm">Integrating hardware and software to produce intelligent solutions in manufacturing, agriculture, transportation, and environment.</p>
                </div>
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-users text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Research & Collaboration</h3>
                    <p class="text-gray-600 text-sm">Conducting multidisciplinary research and collaborating with various parties.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- MISSION Section -->
<section id="mission" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800">OUR MISSION</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto">
            <?php if (!empty($mission)): ?>
                <?php foreach ($mission as $item): ?>
                    <div class="bg-white p-8 rounded-xl shadow-lg card-hover" data-aos="fade-up">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                <?php if (!empty($item['icon'])): ?>
                                    <i class="<?php echo htmlspecialchars($item['icon']); ?> text-xl text-white"></i>
                                <?php else: ?>
                                    <i class="fas fa-bullseye text-xl text-white"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold mb-3 text-gray-800"><?php echo htmlspecialchars($item['judul']); ?></h3>
                                <p class="text-gray-600 leading-relaxed"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default Mission Items -->
                <div class="bg-white p-8 rounded-xl shadow-lg card-hover" data-aos="fade-up">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-rocket text-xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Informatics Technology Application</h3>
                            <p class="text-gray-600 leading-relaxed">Applying algorithms, data processing, and distributed systems for innovative technological solutions to improve productivity and information accessibility.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-xl shadow-lg card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-industry text-xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Technological Innovation for Industry 4.0 Solution</h3>
                            <p class="text-gray-600 leading-relaxed">Providing an Industry 4.0 solutions through innovative automation technologies, system integration, and efficient real-time data processing.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- PRIORITY RESEARCH TOPICS Section -->
<section id="research" class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800">PRIORITY RESEARCH TOPICS</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            <?php if (!empty($researchTopics)): ?>
                <?php foreach ($researchTopics as $research): ?>
                    <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">
                                <?php echo htmlspecialchars($research['nomor']); ?>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 mb-2 leading-tight"><?php echo htmlspecialchars($research['judul']); ?></h3>
                                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($research['deskripsi']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default Research Topics -->
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">1</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Intelligent Self-learning of Computer Programming</h3>
                            <p class="text-gray-600 text-sm">Web, gamifikasi, dan scaffolding</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">2</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Smartfarming (Indoor and Outdoor)</h3>
                            <p class="text-gray-600 text-sm">Based on smart technology and IoT</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">3</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Security Information and Event Management</h3>
                            <p class="text-gray-600 text-sm">Based on Wazuh</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">4</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Decentralized system based on Blockchain</h3>
                            <p class="text-gray-600 text-sm">With Ethereum platform (supply chain, crowdfunding, dan smart contract)</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">5</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Financial support technology</h3>
                            <p class="text-gray-600 text-sm">Decentralization with blockchain and predictive system using data analysis</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="500">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">6</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Asset protection and digitization</h3>
                            <p class="text-gray-600 text-sm">Using Electroencephalograph (EEG)</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-l-4 border-blue-600" data-aos="fade-up" data-aos-delay="600">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center font-bold text-white text-xl">7</div>
                        <div>
                            <h3 class="font-bold text-gray-800 mb-2">Digital map-based reporting system</h3>
                            <p class="text-gray-600 text-sm">For government infrastructure (roads, irrigation, etc.)</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- BLUEPRINT Section -->
<section id="blueprint" class="py-20 bg-gradient-to-br from-blue-50 via-white to-blue-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800">BLUEPRINT</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-2xl mx-auto">Peta Rencana Pengembangan Laboratorium</p>
        </div>
        
        <div class="max-w-5xl mx-auto">
            <!-- Blueprint Diagram -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12" data-aos="fade-up">
                <div class="relative">
                    <!-- Center Circle - BLUEPRINT -->
                    <div class="flex justify-center items-center mb-12">
                        <div class="w-48 h-48 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full flex items-center justify-center shadow-xl">
                            <div class="text-center text-white">
                                <i class="fas fa-drafting-compass text-4xl mb-2"></i>
                                <h3 class="text-2xl font-bold">BLUEPRINT</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Blueprint Items in Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Education Technology -->
                        <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg card-hover border-t-4 border-blue-600" data-aos="fade-up">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-graduation-cap text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">EDUCATION TECHNOLOGY</h4>
                            <p class="text-sm text-gray-600">Mengembangkan e-LMS (Learning Management System) dengan teknologi interaktif dan adaptif</p>
                        </div>
                        
                        <!-- Smart Farming -->
                        <div class="bg-gradient-to-br from-green-50 to-white p-6 rounded-xl shadow-lg card-hover border-t-4 border-green-600" data-aos="fade-up" data-aos-delay="100">
                            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-leaf text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">SMART FARMING</h4>
                            <p class="text-sm text-gray-600">IoT sensors, data analysis, and software to optimize agriculture</p>
                        </div>
                        
                        <!-- Information Security -->
                        <div class="bg-gradient-to-br from-red-50 to-white p-6 rounded-xl shadow-lg card-hover border-t-4 border-red-600" data-aos="fade-up" data-aos-delay="200">
                            <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">INFORMATION SECURITY</h4>
                            <p class="text-sm text-gray-600">SIEM security, threat detection and cyber event security</p>
                        </div>
                        
                        <!-- Distributed Technology -->
                        <div class="bg-gradient-to-br from-purple-50 to-white p-6 rounded-xl shadow-lg card-hover border-t-4 border-purple-600" data-aos="fade-up" data-aos-delay="300">
                            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-network-wired text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">DISTRIBUTED TECHNOLOGY</h4>
                            <p class="text-sm text-gray-600">Distributed architecture for decentralized systems</p>
                        </div>
                        
                        <!-- Financial Technology -->
                        <div class="bg-gradient-to-br from-yellow-50 to-white p-6 rounded-xl shadow-lg card-hover border-t-4 border-yellow-600" data-aos="fade-up" data-aos-delay="400">
                            <div class="w-12 h-12 bg-yellow-600 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-coins text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">FINANCIAL TECHNOLOGY (FINTECH)</h4>
                            <p class="text-sm text-gray-600">Blockchain technology and predictive system for payment</p>
                        </div>
                        
                        <!-- Research & Collaboration -->
                        <div class="bg-gradient-to-br from-indigo-50 to-white p-6 rounded-xl shadow-lg card-hover border-t-4 border-indigo-600" data-aos="fade-up" data-aos-delay="500">
                            <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-2">RESEARCH & COLLABORATION</h4>
                            <p class="text-sm text-gray-600">Multidisciplinary research and industry partnerships</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<button id="scrollTopBtn" class="scroll-top"><i class="fa-solid fa-arrow-up"></i></button>


<?php include 'includes/footer.php'; ?>
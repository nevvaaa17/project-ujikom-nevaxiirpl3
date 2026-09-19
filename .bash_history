exit
docker run --rm -u "$(id -u):$(id -g)" -v "${PWD}:/app" -w /app composer create-project laravel/laravel:^12 backend
docker compose up
cd backend
docker exec -it laravel-api composer require laravel/sanctum
docker exec -it laravel-api php artisan install:api
docker exec -it laravel-api composer require barryvdh/laravel-dompdf
docker exec -it laravel-api php artisan storage:link
docker exec -it laravel-api php artisan make:migration 02_create_kategori_table.php 
docker exec -it laravel-api php artisan make:migration 03_create_alat_table.php
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:migration 06_create_pengembalian_table.php
docker exec -it laravel-api php artisan make:migration 07_create_log_aktivitas_table.php
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan migrate 
docker compose config
cat -n /home/neva17/docker-compose.yml
ls -l /home/neva17
docker compose config
docker compose up --build
docker compose up
docker exec -it laravel-api php artisan make:model Kategori.php 
docker exec -it laravel-api php artisan make:model Alat.php
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:model Peminjaman.php 
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:model DetilPinjam.php Lakukan impor trait/kelas yang dipergunakan pada model DetilPinjam dan perubaha
docker exec -it laravel-api php artisan make:model DetilPinjam.php 
docker exec -it laravel-api php artisan make:model Pengembalian.php 
docker exec -it laravel-api php artisan make:model LogAktivitas.php 
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:seeder UserSeeder 
docker exec -it laravel-api php artisan make:seeder KategoriSeeder 
docker exec -it laravel-api php artisan make:seeder AlatSeeder 
pada terminal: docker exec -it laravel-api php artisan make:seeder PeminjamanSeeder 
docker exec -it laravel-api php artisan make:seeder PeminjamanSeeder 
docker exec -it laravel-api php artisan make:seeder DetailPinjamSeeder 
docker exec -it laravel-api php artisan make:seeder PengembalianSeeder 
docker exec -it laravel-api php artisan make:seeder LogAktivitasSeeder 
sudo chow -R $USER $USER .
sudo chown -R $USER $USER .
docker exec -it laravel-api php artisan db:seed
docker exec -it laravel-api php artisan migrate:fresh --seed
docker exec -it laravel-api composer dump-autoload
docker exec -it laravel-api php artisan migrate:fresh --seed
docker exec -it laravel-api composer dump-autoload
docker exec -it laravel-api php artisan migrate:fresh --seed
docker exec -it laravel-api php artisan db:seed 
Field 'password' doesn't have a default value
php artisan migrate:fresh --seed
php artisan migrate:fresh --seed
v
cd NEVA17
cd neva17
cd NEVA17
sudo apt update && sudo apt install zip -y
docker exec -it laravel-api php artisan migrate:fresh --seed
cd NEVA17
cd neva17
cd NEVA17
sudo apt update && sudo apt install zip -y
pwd
ls -la
find ~ -type d -name "NEVA17"
cd NEVA17
sudo apt update && sudo apt install zip -y
zip -r backend-backup.zip backend -x "backend/vendor/*"
sudo chown $USER $USER .
sudo chown -R $USER $USER .
docker exec -it laravel-api make:middleware CheckRole
docker exec -it laravel-api php artisan make:middleware CheckRole
docker exec -it laravel-api php artisan make:request Auth/LoginRequest 
docker exec -it laravel-api php artisan make:resource UserResource 
sudo chown -R $USER $USER .
docker exec -it laravel-api php artisan make:middleware IsAdmin 
sudo chown -R $USER $USER .
docker exec -it laravel-api php artisan make:middleware IsPetugas 
docker exec -it laravel-api php artisan make:middleware IsPeminjam 
sudo chown -R $USER $USER .
docker exec -it laravel- api php artisan make:controller API/AuthController 
docker exec -it laravel-api php artisan make:controller API/AuthController
sudo chown -R $USER $USER .
docker exec -it laravel-api php artisan make:controller AdminController
docker exec -it laravel-api php artisan make:controller PetugasController
docker exec -it laravel-api php artisan make:controller PeminjamController
sudo chown -R $USER $USER .
docker exec -it laravel-api php artisan make:controller AuthController
sudo chown -R $USER $USER .
php artisan route:clear
php artisan route:list
docker exec -it laravel-api php artisan make:controller API/AdminController
docker exec -it laravel-api php artisan vendor:publish --tag=laravel-pagination 
php artisan optimize:clear
./vendor/bin/sail artisan optimize:clear
docker ps
php artisan route:list
php artisan optimize:clear
php artisan route:list
php artisan route:clear
php artisan migrate:fresh
php artisan route:list
docker exec -it laravel-api php artisan make:resource KategoriResource 
docker exec -it laravel-api php artisan make:request Kategori/StoreKategoriRequest  
docker exec -it laravel- api php artisan make:request Kategori/UpdateKategoriRequest 
sudo chown -R $USER:$USER
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:request Kategori/UpdateKategoriRequest 
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:controller API/KategoriController --api 
sudo chown -R $USER:$USER .
php artisan view:clear
php artisan route:clear
php artisan cache:clear
php artisan route:clear
php artisan route:list --path=admin/pengembalian
git init
git commit -m "first commit"
git remote add origin https://github.com/nevvaaa17/project-ujikom-nevaxiirpl3.git
git branch -M main
git push -u origin main
git config --global user.name "Neva"
git config --global user.email "nevasetiawan59@gmail.com"
git add .
git commit -m "first commit"
git push -u origin main
cd NEVA17
cd neva17
cd backend
git push -u origin main
git add .
git init
git inn
git init
git commit -m "pengembalian"
git add .
git commit "pengembalian"
git commit -m "pengembalian"
git push -u origin main
git push  origin main
git push -u origin main
git remote add origin https://github.com/nevvaaa17/project-ujikom-nevaxiirpl3.git
git push -u origin main
git pull origin main --allow-unrelated-histories
git branch -M main
git push -u origin main
docker exec -it laravel-api php artisan make:Observer alatObserver --model=alat
sudo chown -R $USER:USER .
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:controller PetugasController
rm -rf resources/views/petugas/peminjaman/pengembalian
mkdir -p resources/views/petugas/pengembalian
touch resources/views/petugas/pengembalian/index.blade.php
cd /path/ke/folder-proyek-laravel
mkdir -p resources/views/petugas/pengembalian
touch resources/views/petugas/pengembalian/index.blade.php
php artisan make:observer PeminjamanObserver --model=Peminjaman
php artisan make:observer PengembalianObserver --model=Pengembalian
php artisan make:observer AlatObserver --model=Alat
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan make:resource KategoriResource
docker compose exec laravel-api php artisan make:resource KategoriResource
cd path/to/nama-project-kamu
docker compose exec laravel-api php artisan make:resource KategoriResource
docker compose exec laravel-api php artisan route:clear
docker compose exec laravel-api php artisan config:clear
docker compose exec laravel-api php artisan cache:clear
docker compose exec laravel-api php artisan optimize:clear
docker compose restart laravel-api
cd path/ke/folder-proyek-kamu
docker compose exec laravel-api php artisan optimize:clear
cd /mnt/c/
docker compose exec laravel-api php artisan optimize:clear
cd ~
docker compose exec laravel-api php artisan optimize:clear
docker exec -it laravel-api php artisan make:resource AlatResource 
docker exec -it laravel-api php artisan make:resource KategoriResource 
docker exec -it laravel-api php artisan make:request Alat/UpdateAlatRequest 
docker exec -it laravel-api php artisan make:controller API/AlatController --api 
sudo chown -R $USER:$USER .
docker exec -it laravel-api php artisan make:resource KategoriResource

Bonus Phase:

🧰 What You'll Need
✅ Tools:
VirtualBox


Ubuntu Server ISO (or Debian, CentOS, etc.)


Laravel project ready



⚙️ Step-by-Step: Laravel in VirtualBox (Ubuntu Server Example)

🏗️ 1. Create and Install the VM
Open VirtualBox → New VM → Choose Ubuntu (64-bit)


Allocate:


~2 GB RAM


~20 GB disk (dynamically allocated is fine)


Mount Ubuntu Server ISO and install the OS (basic LAMP stack or minimal)



🌐 2. Set Up Networking (Optional)
Set Network Adapter to:


Bridged Adapter (to get a LAN IP)


OR use NAT with port forwarding (e.g., forward guest port 80 to host port 8080)



🔧 3. Install Laravel Requirements Inside VM
👉 Update & install packages:

sudo apt update && sudo apt upgrade -y
sudo apt install php php-mbstring php-xml php-bcmath php-curl php-zip php-mysql unzip curl git nginx mysql-server composer -y


👉 Optional: Install PHP extensions based on Laravel needs:

sudo apt install php-cli php-common php-tokenizer php-pdo php-gd php-intl php-readline php-soap -y

📂 4. Deploy Laravel

Use SCP, VirtualBox shared folder, or upload via SSH.

⚙️ 5. Configure Nginx (or Apache)
👉 Sample Nginx config (/etc/nginx/sites-available/laravel):

server {
	listen 80;
	server_name localhost;

	root /var/www/your-laravel-app/public;
	index index.php index.html;

	location / {
    	try_files $uri $uri/ /index.php?$query_string;
	}

	location ~ \.php$ {
    	include snippets/fastcgi-php.conf;
    	fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;  # adjust PHP version
	}

	location ~ /\.ht {
    	deny all;
	}
}



sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo systemctl reload nginx



🛠️ 6. Set Permissions and Env Config

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache


✅ 7. Access from Browser
If bridged: go to http://<VM-IP>


If NAT w/ port forwarding: go to http://localhost:8080

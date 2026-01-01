{ pkgs }: {
	deps = [
   pkgs.unzip
   pkgs.php82Extensions.gd
   pkgs.php82Extensions.mbstring
   pkgs.php82Extensions.pgsql
   pkgs.php82Extensions.pdo_pgsql
   pkgs.php82Extensions.pdo
   pkgs.imagemagick
		pkgs.php82
	];
}
#!/bin/bash

# Source (relative) directory locations on server:
source server-directory.loc

echo -e "Comparing the 'site' directory:"
echo "Files in local, not present on remote (excluding .gitignore):"
rsync -rin --ignore-existing --exclude '.gitignore'  $LOCAL_site/ $SERVER_site/
echo "Files in remote, not present on local:"
rsync -rin --ignore-existing --exclude /images/ --exclude /files/ --exclude /sandbox/ --exclude /administration/files/ --exclude /cgi-bin/ $SERVER_site/ $LOCAL_site/
echo "Files in local and remote, which differ:"
rsync -rinc --existing $SERVER_site/ $LOCAL_site/

echo -e "\nComparing the 'hiddenfiles' directory:"
echo "Files in local, not present on remote (excluding .gitignore):"
rsync -rin --ignore-existing --exclude '.gitignore' $LOCAL_hiddenfiles/ $SERVER_hiddenfiles/
echo "Files in remote, not present on local:"
rsync -rin --ignore-existing $SERVER_hiddenfiles/ $LOCAL_hiddenfiles/
echo "Files in local and remote, which differ (excluding blogdb_user.php):"
rsync -rinc --existing --exclude 'blogdb_user.php' $SERVER_hiddenfiles/ $LOCAL_hiddenfiles/

echo -e "\nInterpret rsync output: http://www.staroceans.org/e-book/understanding-the-output-of-rsync-itemize-changes.html"
echo -e "Rsync Legend:\n> = to remote,  < = to local, f = file,\nc = checksum difference, s = size difference,\nT = modification time difference."

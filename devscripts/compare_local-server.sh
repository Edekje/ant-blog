#!/bin/bash

# Location of HTTP server website directory,
# must be explicitly saved in the server-directory.loc file.
# should be of form myaccount@server.com:domains/mysitelocation/
SERVER=`cat server-directory.loc`

# Relative locations of server and local directories:
SERVER_site="${SERVER}public_html/sandbox/site"
SERVER_hiddenfiles="${SERVER}hiddenfiles"
LOCAL_site="../site"
LOCAL_hiddenfiles="../hiddenfiles"

echo -e "Comparing the 'site' directory:"
echo "Files in local, not present on remote (excluding .gitignore):"
rsync -rin --ignore-existing --exclude '.gitignore'  $LOCAL_site/ $SERVER_site/
echo "Files in remote, not present on local:"
rsync -rin --ignore-existing --exclude /images/ --exclude /files/ $SERVER_site/ $LOCAL_site/
echo "Files in local and remote, which differ:"
rsync -rinc --existing $SERVER_site/ $LOCAL_site/

echo -e "\nComparing the 'hiddenfiles' directory:"
echo "Files in local, not present on remote (excluding .gitignore):"
rsync -rin --ignore-existing --exclude '.gitignore' $LOCAL_hiddenfiles/ $SERVER_hiddenfiles/
echo "Files in remote, not present on local:"
rsync -rin --ignore-existing $SERVER_hiddenfiles/ $LOCAL_hiddenfiles/
echo "Files in local and remote, which differ:"
rsync -rinc --existing $SERVER_hiddenfiles/ $LOCAL_hiddenfiles/

echo -e "\nInterpret rsync output: http://www.staroceans.org/e-book/understanding-the-output-of-rsync-itemize-changes.html"
echo -e "Rsync Legend:\n> = to remote,  < = to local, f = file,\nc = checksum difference, s = size difference,\nT = modification time difference."

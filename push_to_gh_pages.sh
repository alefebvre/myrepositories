#!/bin/bash
start=$(date +%s)

function error_exit
{
        echo -e "\e[01;31m$1\e[00m" 1>&2
        exit 1
}



if [ "$POST_BUILD" == "true" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ]; then
        echo -e "Starting to update gh-pages"
        # copy data we're interested in to other place
        cp -Rv build/result/coverage $HOME/build/coverage
        cp -Rv build/result/docs $HOME/build/docs
  
        # go to home and setup git
        cd $HOME
        git config --global user.email "aurelien.lefebvre@viacesi.fr"
		git config --global user.name "Travis"
        git config --global push.default matching
       
        # using token clone gh-pages branch
        git clone --quiet https://${GH_TOKEN}@github.com/alefebvre/myrepositories.git repo > /dev/null || error_exit "Error cloning the repository";

        # go into repo anc copy data
        cd repo
        
        # checkout gh-pages
        git checkout gh-pages
        
        # Remove "old" stuff
        rm -rf coverage/
        rm -rf docs/
        

        # copy stuff
        cp -Rv $HOME/build/coverage/ coverage/
        cp -Rv $HOME/build/docs/ docs/
        cp -Rv $HOME/build/phploc.txt phploc.txt

        # add, commit and push files
        git add .
        git commit -m "Travis build $TRAVIS_BUILD_NUMBER pushed to gh-pages"
        git push > /dev/null
        echo -e "Pushed to GitHub"
else
        echo "Something went wrong..."
        echo "Additional info:"
        echo "$TRAVIS_PULL_REQUEST"
        echo "$POST_BUILD"
        ls -la
fi

end=$(date +%s)
elapsed=$(( $end - $start ))
minutes=$(( $elapsed / 60 ))
seconds=$(( $elapsed % 60 ))
echo "Post-Build process finished in $minutes minute(s) and $seconds seconds"
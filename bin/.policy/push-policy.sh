#!/bin/bash

## Policy for git push hook
################################################################################

source ${PWD}/bin/.helper/output

################################################################################
## Options

## Git settings and validation
PROTECTED_BRANCH='develop'
PROTECTED_BRANCHES=('develop' 'master')
CURRENT_BRANCH=$(git symbolic-ref HEAD | sed -e 's,.*/\(.*\),\1,')
PUSH_CMD=$(ps -ocommand= -p $PPID)
IS_DESTRUCTIVE='force|delete|\-f'
WILL_REMOVE_PROTECTED_BRANCH=':'${PROTECTED_BRANCH}
ERRORS=()

## Check commands & error messages
PHPMD_ERR="Code mess detection failed: see var/log/phpmd.log or run 'bin/qa md'."
PHPMD_CMD="./vendor/bin/phpmd src text codesize,design,controversial --exclude src/Migrations --strict > var/log/phpmd.log 2>&1"
PHPCS_ERR="Code standard check failed: see var/log/phpcs.log or run 'bin/qa cs'."
PHPCS_CMD="./vendor/bin/phpcs --standard=PSR2 --extensions=php --ignore=src/Migrations/* src > var/log/phpcs.log 2>&1"

################################################################################
## Functions

array_contains () {
  local e match="$1"
  shift
  for e; do [[ "$e" == "$match" ]] && return 0; done
  return 1
}

check_policy(){
    if [ $# -eq 0 ] || [ -z "$1" ]; then
        echo "Missing argument for check_policy."
        exit 1;
    fi

    if [ $(cd $PWD && eval ${1};echo $?) != 0 ]; then
        printc " ✗ Fail\\n" "red"
        if [ -z "$2" ]; then
            ERRORS+=("Policy check failed.")
        else
            ERRORS+=("${2}")
        fi
    else
        printc " ✓ OK\\n" "green"
    fi
}

check_git(){
    if [[ ${PUSH_CMD} =~ $IS_DESTRUCTIVE ]] && [ ${CURRENT_BRANCH} = ${PROTECTED_BRANCH} ]; then
      ERRORS+=("Never force push or delete the '${PROTECTED_BRANCH}' branch!")
    fi

    if [[ ${PUSH_CMD} =~ $IS_DESTRUCTIVE ]] && [[ ${PUSH_CMD} =~ ${PROTECTED_BRANCH} ]]; then
      ERRORS+=("Never force push or delete the '${PROTECTED_BRANCH}' branch!")
    fi

    if [[ ${PUSH_CMD} =~ ${WILL_REMOVE_PROTECTED_BRANCH} ]]; then
      ERRORS+=("Git policy breach: deleting '${PROTECTED_BRANCH}' branch not allowed!")
    fi
}

################################################################################
## Main

CHECK=$(array_contains "${CURRENT_BRANCH}" "${PROTECTED_BRANCHES[@]}";echo $?)

if [ ${CHECK} != 0 ]; then
    exit 0;
fi

printc "\\n➜  Compliance checks required for branch $(tput bold)${CURRENT_BRANCH}:\\n" "blue"
printf "➜  Checking git push policy........."
check_git

if [ ${#ERRORS[@]} -eq 0 ]; then
    printc " ✓ OK\\n" "green"
else
    printc " ✗ Fail\\n" "red"
fi

printf "➜  Looking for PHP code............."

if [ ! -d src ]; then
    printc " ✓ No code\\n" "blue"

elif [ ! -f ./vendor/bin/phpcs ] || [ ! -f ./vendor/bin/phpmd ] ; then
    printc " ✗\\n" "red"
    ERRORS+=("Code quality tools not found, checks skipped!")
else
    printc " ✓ OK\\n" "green"
    printf "➜  Checking code standard..........."
    check_policy "${PHPCS_CMD}" "${PHPCS_ERR}"

    printf "➜  Detecting mess in code..........."
    check_policy "${PHPMD_CMD}" "${PHPMD_ERR}"
fi

if [ ${#ERRORS[@]} -eq 0 ]; then
    printf -- "\\n"
    printc "➜  SUCCESS - All checks passed.\\n\\n" "green"
else
    printf -- "\\n"
    printc "$(tput bold)➜  REJECTED - Compliance errors: \\n$(tput sgr0)" "red"

    for i in "${ERRORS[@]}"
    do
        :
        printc "➜  " "red"
        printf "${i}\\n"
    done

    printf "\\n"
    exit 1
fi

exit 0
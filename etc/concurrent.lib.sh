#!/usr/bin/env bash

concurrent() (
    #
    # General Utilities
    #

    __crt__error() {
        echo "ERROR (concurrent): ${1}" 1>&2
        exit 1
    }

    __crt__is_dry_run() {
        [[ -n "${CONCURRENT_DRY_RUN}" ]]
    }

    __crt__hide_failure() {
        "${@}" 2> /dev/null || :
    }

    #
    # Compatibility Check
    #

    if [[ -z "${BASH_VERSINFO[@]}" || "${BASH_VERSINFO[0]}" -lt 4 || "${BASH_VERSINFO[1]}" -lt 2 ]]; then
        __crt__error "Requires Bash version 4.2 for 'declare -g' (you have ${BASH_VERSION:-a different shell})"
    fi

    #
    # Help and Usage
    #

    __crt__help__version='concurrent 2.3.1'

    __crt__help__usage="concurrent - Run tasks in parallel and display pretty output as they complete.

        Usage:
          concurrent \\
              (- TASK COMMAND [ARGS...] | --and-then)... \\
              [--sequential | [(--require-all | (--require TASK)...) \\
                               ( --before-all | ( --before TASK)...)...]]
          concurrent -h | --help
          concurrent --version

        Options:
          -h --help                 Show this help.
          --version                 Show version.
          - TASK COMMAND [ARGS...]  Define a task named TASK for running COMMAND with ARGS.
          --and-then                Insert between tasks to create groups of concurrent tasks.
          --sequential              Each task requires the previous task.
          --require TASK            Require a TASK to complete successfully...
          --before TASK             ...before another TASK.
          --require-all             Shortcut for requiring all tasks.
          --before-all              Given tasks are prerequisites to all others.

        Failed Tasks:
          If a task fails, all dependent tasks (and their dependent tasks, and so on) are
          immediately marked 'SKIP'. The status and output of all failed and skipped tasks
          are displayed at the end. The exit status will be 1.

        Examples:
          # Run three tasks concurrently.
          concurrent \\
              - 'My long task'   sleep 10 \\
              - 'My medium task' sleep 5  \\
              - 'My short task'  sleep 1

          # Run three tasks sequentially.
          concurrent \\
              - 'My long task'   sleep 10 \\
              - 'My medium task' sleep 5  \\
              - 'My short task'  sleep 1  \\
              --sequential

          # Start the medium task *after* the short task succeeds.
          concurrent \\
              - 'My long task'   sleep 10 \\
              - 'My medium task' sleep 5  \\
              - 'My short task'  sleep 1  \\
              --require 'My short task'   \\
              --before  'My medium task'

          # Start the short task after *both* other tasks succeed.
              ...
              --require 'My long task'    \\
              --require 'My medium task'  \\
              --before  'My short task'

          # Start the medium task *and* the long task after the short task succeeds.
              ...
              --require 'My short task'   \\
              --before  'My medium task'  \\
              --before  'My long task'

          # If your command has a '-' argument, you can use a different task delimiter.
          concurrent \\
              + 'My long task'   wget -O - ... \\
              + 'My medium task' sleep 5  \\
              + 'My short task'  sleep 1

        Settable Environment Variables:
          CONCURRENT_DRY_RUN (default: unset)
            Setting this to any non-empty value causes 'dry-run' mode to be enabled.
            In this mode, all task commands are replaced with 'sleep 3'. This is useful
            for testing that the dependencies between tasks are correctly specified.

          CONCURRENT_LIMIT (default: 50)
            Specifies the number of tasks which are allowed to be actively running at
            any time. For unlimited concurrency, set this to an integer less than 1.

          CONCURRENT_COMPACT (default: 0)
            Setting this to a non-zero value causes the 'compact display' mode to be
            activated. In this mode, each task is represented by a single character
            instead of an entire line. This mode is automatically activated if the
            number of tasks exceed the terminal height.

          CONCURRENT_LOG_DIR (default: \$PWD/.logs/\$(date +'%F@%T'))
            Optionally change the directory in which all tasks are logged.

        Informational Environment Variables:
          CONCURRENT_DEPTH
            The current nesting depth of this 'concurrent' instance, starting at 0.

          CONCURRENT_COMMAND
            An array of the arguments being run for a task.

          CONCURRENT_TASK_NAME
            The name of the task being run.

        Requirements:
          bash >= 4.2, cat, cp, date, mkdir, mkfifo, mktemp, mv, sed, tail, tput

        Author:
          Matthew Tardiff <mattrix@gmail.com>

        License:
          MIT

        Version:
          ${__crt__help__version}

        URL:
          https://github.com/themattrix/bash-concurrent"

    __crt__help__display_usage_and_exit() {
       sed 's/^        //' <<< "${__crt__help__usage}"
       exit 0
    }

    __crt__help__display_version_and_exit() {
       echo "${__crt__help__version}"
       exit 0
    }

    if [[ -z "${1}" ]] || [[ "${1}" == '-h' ]] || [[ "${1}" == '--help' ]]; then
        __crt__help__display_usage_and_exit
    elif [[ "${1}" == '--version' ]]; then
        __crt__help__display_version_and_exit
    fi

    __crt__unset_env() {
        # Unset all concurrent-related configuration environment variables.
        unset CONCURRENT_DRY_RUN
    }

    __crt__unset() {
        local sub_namespace=${1}
        if [[ -n "${sub_namespace}" ]]; then
            local namespace="__crt__${sub_namespace}__"
        else
            local namespace="__crt__"
        fi
        # shellcheck disable=SC2046
        unset -f $(compgen -A function "${namespace}")
        # shellcheck disable=SC2046
        unset $(compgen -v "${namespace}")
    }

    __crt__unset 'help'

    #
    # Settings
    #

    __crt__ORIG_PWD=${PWD}
    __crt__ORIG_OLDPWD=${OLDPWD}
    __crt__ORIG_BASHOPTS=${BASHOPTS}
    __crt__ORIG_SHELLOPTS=${SHELLOPTS}

    __crt__set_original_pwd() {
        cd "${__crt__ORIG_PWD}"
        export OLDPWD=${__crt__ORIG_OLDPWD}
    }

    __crt__set_our_shell_options() {
        set -o errexit      # Exit on a failed command...
        set -o pipefail     # ...even if that command is in a pipeline.
        shopt -s nullglob   # Empty glob evaluates to nothing instead of itself
    }

    __crt__set_original_shell_options() {
        __crt__set_our_shell_options
        [[ "${__crt__ORIG_SHELLOPTS}" == *errexit*  ]] || set +o errexit
        [[ "${__crt__ORIG_SHELLOPTS}" == *pipefail* ]] || set +o pipefail
        [[ "${__crt__ORIG_BASHOPTS}"  == *nullglob* ]] || shopt -u nullglob
    }

    __crt__set_our_shell_options

    #
    # Task Management
    #

    __crt__is_task_started() {
        [[ -z "${__crt__pending[${1}]}" ]]
    }

    __crt__is_task_done() {
        [[ -n "${__crt__codes[${1}]}" ]]
    }

    __crt__are_all_tasks_done() {
        [[ "${__crt__running_task_count}" -eq 0 ]]
    }

    __crt__is_task_running() {
        __crt__is_task_started "${1}" && ! __crt__is_task_done "${1}"
    }

    __crt__is_under_concurrent_limit() {
        [[ "${CONCURRENT_LIMIT}" -lt 1 || "${__crt__running_task_count}" -lt "${CONCURRENT_LIMIT}" ]]
    }

    __crt__clear_event_pipe() {
        > "${__crt__event_pipe}"
    }

    __crt__name_index() {
        local name=${1}
        local i
        for i in "${!__crt__names[@]}"; do
            if [[ "${__crt__names[${i}]}" == "${name}" ]]; then
                printf '%s' "${i}"
                return
            fi
        done
        __crt__error "Failed to find task named '${name}'"
    }

    __crt__is_task_allowed_to_start() {
        # A task is allowed to start if:
        #   1. it has not already started, and if
        #   2. all prereq tasks have succeeded, and if
        #   3. the process has not been interrupted.
        # If any prereqs have failed or have been skipped, then this task will
        # be skipped.

        local task=${1}
        if __crt__is_task_started "${task}"; then
            return 1  # cannot start again
        fi

        local requires
        local prereqs="__crt__prereqs_${task}[@]"
        for requires in "${!prereqs}"; do
            if [[ -z "${__crt__codes[${requires}]}" ]]; then
                return 1
            elif [[ "${__crt__codes[${requires}]}" != "0" ]]; then
                __crt__skip_task "${task}" "${__crt__names[${requires}]}"
                return 1
            fi
        done

        if [[ "${__crt__interrupted}" -eq 1 ]]; then
            __crt__mark_task_as_interrupted "${task}"
            return 1
        fi

        # All prereqs succeeded! This task can be started.
    }

    __crt__mark_task_with_code() {
        local task=${1}
        local code=${2}
        echo "task:${task}:${code}" >> "${__crt__event_pipe}"
    }

    __crt__task_runner() (
        # Do not create real variables for these so that they do not override
        # names from the parent script.
        # $1: task index
        # $2: command args array
        # $3: status dir
        # $4: event pipe
        set -- "${1}" "__crt__command_${1}[@]" "${__crt__status_dir}" "${__crt__event_pipe}"

        # Copy the command over since we're unsetting the __crt__ variables.
        CONCURRENT_COMMAND=("${!2}")

        # Reset any existing signal handlers.
        trap -- - INT EXIT

        # Allow nested tasks to refer to parent tasks.
        CONCURRENT_TASK_NAME+=("${__crt__names[${1}]}")
        export CONCURRENT_TASK_NAME

        set +o errexit  # a failure of the command should not exit the task
        (
            __crt__set_original_pwd
            __crt__set_original_shell_options
            __crt__unset_env
            __crt__unset

            "${CONCURRENT_COMMAND[@]}" 3>&1 &> "${3}/${1}" < /dev/null |
            while read -r meta; do
                printf "meta:%d:%s\n" "${1}" "${meta}" >> "${4}"
            done
        )
        code=$?
        set -o errexit  # ...but other failures should

        __crt__mark_task_with_code "${1}" "${code}"
    )

    __crt__mark_task_as_started() {
        if [[ -n "${__crt__pending[${1}]}" ]]; then
            unset "__crt__pending[${1}]"
            (( __crt__running_task_count++ )) || :
        fi
    }

    __crt__mark_task_as_stopped() {
        local index=${1}
        local code=${2}

        if [[ -z "${__crt__codes[${index}]}" ]]; then
            (( __crt__running_task_count-- )) || :
            if   [[ "${code}" == '0'    ]]; then (( __crt__success_task_count++ ))     || :
            elif [[ "${code}" == 'skip' ]]; then (( __crt__skipped_task_count++ ))     || :
            elif [[ "${code}" == 'int'  ]]; then (( __crt__interrupted_task_count++ )) || :
            else                                 (( __crt__failure_task_count++ ))     || :
            fi
        fi
    }

    __crt__start_task() {
        __crt__task_runner "${1}" &
        __crt__mark_task_as_started "${1}"
        __crt__draw_status "${1}" running
    }

    __crt__start_all_tasks() {
        __crt__draw_initial_tasks
        __crt__move_cursor_to_top
        __crt__start_allowed_tasks
    }

    __crt__mark_task_as_interrupted() {
        __crt__mark_task_as_started "${1}"
        printf '[INTERRUPTED]\n' >> "${__crt__status_dir}/${1}"
        printf 'task:%d:int\n' "${1}" >> "${__crt__event_pipe}"
    }

    __crt__mark_all_running_tasks_as_interrupted() {
        local i
        for (( i = 0; i < __crt__task_count; i++ )); do
            if __crt__is_task_running "${i}"; then
                __crt__mark_task_as_interrupted "${i}"
            fi
        done
    }

    __crt__skip_task() {
        __crt__mark_task_as_started "${1}"
        echo "[SKIPPED] Prereq '${2}' failed or was skipped" > "${__crt__status_dir}/${1}"
        __crt__mark_task_with_code "${1}" skip
    }

    __crt__start_allowed_tasks() {
        local __crt__i
        for __crt__i in "${__crt__pending[@]}"; do
            __crt__is_under_concurrent_limit || break
            if __crt__is_task_allowed_to_start "${__crt__i}"; then
                __crt__start_task "${__crt__i}"
            fi
        done
    }

    __crt__save_stdin_stream() {
        exec 4<&0  # duplicate stdin stream to fd 4
    }

    __crt__restore_stdin_stream() {
        exec 0<&4  # restore original stdin stream from fd 4
    }

    __crt__wait_for_all_tasks() {
        __crt__start_animation
        __crt__save_stdin_stream
        __crt__run_event_loop
        __crt__status_cleanup
        __crt__stop_animation
        wait  # wait for all (completed) tasks
        __crt__restore_stdin_stream
    }

    __crt__run_event_loop() {
        # Main event loop! Each line read from the event pipe is an event to
        # handle. We can exit the loop once all tasks have completed.
        local __crt__event
        local __crt__tail_pipe="${__crt__status_dir}/tail-pipe"

        rm -f  "${__crt__tail_pipe}"
        mkfifo "${__crt__tail_pipe}"

        tail -n +0 -f "${__crt__event_pipe}" >> "${__crt__tail_pipe}" &
        __crt__tail_pid=$!

        while read -r __crt__event; do
            if [[ "${__crt__event}" == task:* ]]; then
                __crt__handle_done_task "${__crt__event#task:}"
                if __crt__are_all_tasks_done; then
                    break
                fi
            elif [[ "${__crt__event}" == anim:* ]]; then
                __crt__update_running_status_frames
            elif [[ "${__crt__event}" == meta:* ]]; then
                __crt__manage_meta "${__crt__event#meta:}"
            fi
        done < "${__crt__tail_pipe}"

        __crt__cleanup_event_loop
    }

    __crt__cleanup_event_loop() {
        if [[ -n "${__crt__tail_pid}" ]]; then
            __crt__hide_failure kill "${__crt__tail_pid}"
            __crt__hide_failure wait "${__crt__tail_pid}"
        fi
    }

    __crt__handle_done_task() {
        local index=${1%%:*}
        local code=${1#*:}
        __crt__mark_task_as_stopped "${index}" "${code}"
        __crt__codes["${index}"]=${code}
        __crt__draw_status "${index}" "${code}"
        cp -- "${__crt__status_dir}/${index}" "${CONCURRENT_LOG_DIR}/${index}. ${__crt__names[${index}]//\//-} (${code}).log"
        if [[ "${code}" != "0" ]]; then
            __crt__final_status=1
        fi
        __crt__start_allowed_tasks
    }

    __crt__start_animation() {
        __crt__update_running_status_frames
        while true; do
            sleep "${__crt__seconds_between_frames}"
            echo "anim:" >> "${__crt__event_pipe}"
        done &
        __crt__animation_pid=$!
    }

    __crt__stop_animation() {
        __crt__hide_failure kill "${__crt__animation_pid}"
        __crt__hide_failure wait "${__crt__animation_pid}"
    }

    __crt__manage_meta() {
        local index=${1%%:*}
        local meta=${1#*:}
        __crt__meta["${index}"]=${meta}
        __crt__draw_meta "${index}"
    }

    #
    # Argument Parsing
    #

    __crt__names=()        # task names by index
    __crt__meta=()         # metadata strings by index
    __crt__pending=()      # indexes of tasks which haven't been started yet
    __crt__codes=()        # task exit codes (unset, 0-255, 'skip', or 'int') by index
    __crt__groups=()       # array of task indexes before which --and-then flags were specified
    __crt__task_count=0    # total number of tasks
    __crt__final_status=0  # 0 if all tasks succeeded, 1 otherwise
    __crt__interrupted=0   # 1 if script has been interrupted, 0 otherwise

    # Only allow this many tasks running at a time.
    export CONCURRENT_LIMIT=${CONCURRENT_LIMIT:-50}
    __crt__running_task_count=0

    __crt__success_task_count=0
    __crt__failure_task_count=0
    __crt__skipped_task_count=0
    __crt__interrupted_task_count=0

    # Arrays of command arguments by task index <T>:
    #   __crt__command_<T>=(...)
    #
    # Arrays of prerequisite task indices by task index <T>:
    #   __crt__prereqs_<T>=(...)
    #
    # These are dynamically created during argument parsing since bash doesn't
    # have a concept of nested lists.

    __crt__args__task_delimiter=${1}

    __crt__args__is_task_flag()        { [[ "${1}" == "${__crt__args__task_delimiter}" ]]; }
    __crt__args__is_group_flag()       { [[ "${1}" == "--and-then"    ]]; }
    __crt__args__is_require_flag()     { [[ "${1}" == "--require"     ]]; }
    __crt__args__is_require_all_flag() { [[ "${1}" == "--require-all" ]]; }
    __crt__args__is_before_flag()      { [[ "${1}" == "--before"      ]]; }
    __crt__args__is_before_all_flag()  { [[ "${1}" == "--before-all"  ]]; }
    __crt__args__is_sequential_flag()  { [[ "${1}" == "--sequential"  ]]; }

    __crt__args__is_flag_starting_section() {
        __crt__args__is_task_flag "${1}" ||
        __crt__args__is_group_flag "${1}" ||
        __crt__args__is_require_flag "${1}" ||
        __crt__args__is_require_all_flag "${1}" ||
        __crt__args__is_sequential_flag "${1}"
    }

    __crt__args__is_item_in_array() {
        local item_to_find=${1}
        local array_name="${2}[@]"
        local i
        for i in "${!array_name}"; do
            if [[ "${i}" == "${item_to_find}" ]]; then return 0; fi
        done
        return 1
    }

    __crt__args__get_tasks_not_in() {
        local these_tasks=${1}
        local other_tasks=()
        local i

        for (( i = 0; i < __crt__task_count; i++ )); do
            __crt__args__is_item_in_array "${i}" "${these_tasks}" || other_tasks=(${other_tasks[@]} ${i})
        done

        __crt__args__fn_result=("${other_tasks[@]}")
    }

    __crt__args__assign_sequential_prereqs() {
        local i
        for (( i = 1; i < __crt__task_count; i++ )); do
            declare -g -a "__crt__prereqs_${i}=($(( i - 1 )))"
        done
    }

    __crt__args__handle_task_flag() {
        set -- "${remaining_args[@]}"

        shift; (( $# )) || __crt__error "expected task name after '-'"
        __crt__names+=("${1}")
        shift; (( $# )) || __crt__error "expected command after task name"
        local args=()
        while (( $# )) && ! __crt__args__is_flag_starting_section "${1}"; do
            args+=("${1}")
            shift
        done
        if __crt__is_dry_run; then
            args=(sleep 3)  # DRY RUN: Sleep for 3 seconds instead of running a real command.
        fi
        declare -g -a "__crt__command_${__crt__task_count}=(\"\${args[@]}\")"
        __crt__pending+=("${__crt__task_count}")
        (( __crt__task_count++ )) || :

        remaining_args=("${@}")
    }

    __crt__args__handle_group_flag() {
        set -- "${remaining_args[@]}"
        shift
        __crt__groups+=("${__crt__task_count}")
        remaining_args=("${@}")
    }

    __crt__args__handle_sequential_flag() {
        set -- "${remaining_args[@]}"
        shift
        __crt__args__assign_sequential_prereqs
        remaining_args=("${@}")
    }

    __crt__args__handle_require_flag() {
        set -- "${remaining_args[@]}"

        local require
        local before

        while (( $# )) && __crt__args__is_require_flag "${1}"; do
            shift; (( $# )) || __crt__error "expected task name after '--require'"
            require=(${require[@]} $(__crt__name_index "${1}"))
            shift
        done

        if __crt__args__is_before_all_flag "${1}"; then
            shift
            __crt__args__get_tasks_not_in 'require'; before=("${__crt__args__fn_result[@]}")
            local b
            for b in "${before[@]}"; do
                declare -g -a "__crt__prereqs_${b}=(\${require[@]})"
            done
        elif __crt__args__is_before_flag "${1}"; then
            while (( $# )) && __crt__args__is_before_flag "${1}"; do
                shift; (( $# )) || __crt__error "expected task name after '--before'"
                before=$(__crt__name_index "${1}")
                shift
                if __crt__args__is_item_in_array "${before}" "require"; then
                    __crt__error "task cannot require itself"
                fi
                declare -g -a "__crt__prereqs_${before}=(\${__crt__prereqs_${before}[@]} \${require[@]})"
            done
        else
            __crt__error "expected '--before' or '--before-all' after '--require-all'"
        fi

        remaining_args=("${@}")
    }

    __crt__args__handle_require_all_flag() {
        set -- "${remaining_args[@]}"

        local require
        local before

        shift
        if __crt__args__is_before_all_flag "${1}"; then
            shift
            __crt__args__assign_sequential_prereqs  # --require-all --before-all is the same as --sequential
        elif __crt__args__is_before_flag "${1}"; then
            before=()
            while (( $# )) && __crt__args__is_before_flag "${1}"; do
                shift; (( $# )) || __crt__error "expected task name after '--before'"
                before=(${before[@]} $(__crt__name_index "${1}"))
                shift
            done
            __crt__args__get_tasks_not_in 'before'; require=("${__crt__args__fn_result[@]}")
            local b
            for b in "${before[@]}"; do
                declare -g -a "__crt__prereqs_${b}=(\${require[@]})"
            done
        else
            __crt__error "expected '--before' or '--before-all' after '--require-all'"
        fi

        remaining_args=("${@}")
    }

    __crt__args__resolve_group_prereqs() {
        local curr_index
        local task_index
        local curr_group
        local next_group
        local prev_group=0

        # All tasks in group N are prereqs for all tasks in group N+1. If N+1
        # does not exist, use the task count instead.
        for (( curr_index = 0; curr_index < ${#__crt__groups[@]}; curr_index++ )); do
            curr_group=${__crt__groups[${curr_index}]}
            next_group=${__crt__groups[$(( curr_index + 1 ))]:-${__crt__task_count}}
            for (( task_index = curr_group; task_index < next_group; task_index++ )); do
                declare -g -a "__crt__prereqs_${task_index}=(\${__crt__prereqs_${task_index}[@]} {${prev_group}..$(( curr_group - 1 ))})"
            done
            prev_group=${curr_group}
        done

        # No longer need this array up in our business.
        unset __crt__groups
    }

    __crt__args__ensure_no_requirement_loops() (
        # We will do a lightweight dry-run through all of the tasks and make sure we
        # do not get stuck anywhere.
        tasks_started=0

        is_task_allowed_to_start() {
            local task=${1}
            [[ -n "${__crt__pending[${task}]}" ]] || return 1
            local requires
            local prereqs="__crt__prereqs_${task}[@]"
            for requires in "${!prereqs}"; do
                [[ -z "${__crt__pending[${requires}]}" ]] || return 1
            done
        }

        start_allowed_tasks() {
            tasks_started=0
            local i
            for i in "${__crt__pending[@]}"; do
                if is_task_allowed_to_start "${i}"; then
                    start_task "${i}"
                    (( tasks_started++ )) || :
                fi
            done
        }

        start_task() {
            unset "__crt__pending[${1}]"
        }

        while true; do
            start_allowed_tasks
            [[ "${#__crt__pending[@]}" != 0 ]] || break
            [[ "${tasks_started}" -gt 0 ]] || __crt__error "detected requirement loop"
        done
    )

    __crt__args__parse() {
        local remaining_args=("${@}")

        while (( ${#remaining_args} )); do
            if __crt__args__is_task_flag "${remaining_args[0]}"; then
                __crt__args__handle_task_flag
            elif __crt__args__is_group_flag "${remaining_args[0]}"; then
                __crt__args__handle_group_flag
            elif __crt__args__is_require_flag "${remaining_args[0]}"; then
                __crt__args__handle_require_flag
            elif __crt__args__is_require_all_flag "${remaining_args[0]}"; then
                __crt__args__handle_require_all_flag
            elif __crt__args__is_sequential_flag "${remaining_args[0]}"; then
                __crt__args__handle_sequential_flag
            else
                __crt__error "unexpected argument '${remaining_args[0]}'"
            fi
        done

        __crt__args__resolve_group_prereqs
        __crt__args__ensure_no_requirement_loops
        __crt__unset 'args'
    }

    __crt__args__parse "${@}"

    #
    # Status Updates
    #

    __crt__txtred='\e[0;31m' # Red
    __crt__txtgrn='\e[0;32m' # Green
    __crt__txtylw='\e[0;33m' # Yellow
    __crt__txtblu='\e[0;34m' # Blue
    __crt__txtbld='\e[1m'    # Bold
    __crt__txtrst='\e[0m'    # Text Reset
    __crt__txtclr='\e[0K'    # Clear to end of line

    export CONCURRENT_COMPACT=${CONCURRENT_COMPACT:-0}

    __crt__use_compact_status() {
        [[ "${CONCURRENT_COMPACT}" != "0" || "${__crt__task_count}" -ge "$(tput lines)" ]]
    }

    if __crt__use_compact_status; then
        __crt__cols=$(tput cols)

        __crt__draw_meta       () { :; }
        __crt__start_animation () { :; }
        __crt__stop_animation  () { :; }
        __crt__print_failures  () { :; }

        __crt__draw_initial_tasks() {
            # task lines + most recent update lines + summary lines
            local rows=$(( __crt__task_count / __crt__cols + 4 ))
            local row
            for (( row = 0; row < rows; row++ )); do echo; done
            tput cuu "${rows}"
            tput sc
        }

        __crt__move_cursor_to_top() {
            tput rc
        }

        __crt__move_cursor_to_first_task() {
            __crt__move_cursor_to_top
            tput cud 2
        }

        __crt__requires_newline_after_tasks() {
            [[ $(( __crt__task_count % __crt__cols )) -ne 0 ]]
        }

        if __crt__requires_newline_after_tasks; then
            __crt__move_cursor_below_tasks() {
                __crt__move_cursor_to_index "${__crt__task_count}"
                echo
            }
        else
            __crt__move_cursor_below_tasks() {
                __crt__move_cursor_to_index "${__crt__task_count}"
            }
        fi

        __crt__move_cursor_below_status() {
            __crt__move_cursor_below_tasks
            echo
            tput el
            tput cuu 1
        }

        __crt__move_cursor_to_index() {
            local index=${1}
            local col=$(( index % __crt__cols ))
            local row=$(( index / __crt__cols ))
            __crt__move_cursor_to_first_task
            [[ "${col}" -eq 0 ]] || tput cuf "${col}"
            [[ "${row}" -eq 0 ]] || tput cud "${row}"
        }

        __crt__draw_status() {
            local index=${1}
            local code=${2}
            __crt__move_cursor_to_top
            __crt__draw_summary
            __crt__move_cursor_to_index "${index}"
            __crt__draw_task "${code}"
            __crt__move_cursor_below_tasks
            [[ "${code}" == "running" ]] || __crt__draw_recent_verbose_task "${index}" "${code}"
            __crt__move_cursor_to_top
        }

        __crt__draw_task() {
            local code=${1}
            if   [[ "${code}" == "int"     ]]; then printf "${__crt__txtred}%c${__crt__txtrst}" '!'
            elif [[ "${code}" == "skip"    ]]; then printf "${__crt__txtylw}%c${__crt__txtrst}" '-'
            elif [[ "${code}" == "running" ]]; then printf "${__crt__txtblu}%c${__crt__txtrst}" '>'
            elif [[ "${code}" == "0"       ]]; then printf '.'
            else                                    printf "${__crt__txtred}%c${__crt__txtrst}" 'X'
            fi
        }

        __crt__draw_recent_verbose_task() {
            local index=${1}
            local code=${2}
            local meta=${__crt__meta[${index}]}
            if   [[ "${code}" == "int"     ]]; then printf "\n ${__crt__txtred}%s${__crt__txtrst} " 'SIGINT'
            elif [[ "${code}" == "skip"    ]]; then printf "\n ${__crt__txtylw}%s${__crt__txtrst} " ' SKIP '
            elif [[ "${code}" == "0"       ]]; then printf "\n ${__crt__txtgrn}%s${__crt__txtrst} " '  OK  '
            else                                    printf "\n ${__crt__txtred}%s${__crt__txtrst} " 'FAILED'
            fi
            printf "%s" "${__crt__names[${index}]}"
            if [[ -n "${meta}" ]]; then printf " ${__crt__txtbld}%s${__crt__txtrst}" "${meta}"
            fi
            tput el  # clear to the end of the line in case the task previously displayed was longer
        }

        __crt__draw_summary() {
            local percent=$(( ${#__crt__codes[@]} * 100 / __crt__task_count ))
            local success="  ${__crt__success_task_count} passed"
            local failure
            local skipped
            local interrupted
            [[ "${__crt__failure_task_count}"     -eq 0 ]] || failure="  ${__crt__failure_task_count} failed"
            [[ "${__crt__skipped_task_count}"     -eq 0 ]] || skipped="  ${__crt__skipped_task_count} skipped"
            [[ "${__crt__interrupted_task_count}" -eq 0 ]] || interrupted="  ${__crt__interrupted_task_count} interrupted"
            printf " %3d%% %s%s%s%s\n\n" \
                "${percent}" \
                "${success}" \
                "${failure}" \
                "${skipped}" \
                "${interrupted}"
        }
    else
        __crt__seconds_between_frames=1.0
        __crt__running_status_current_frame=0
        __crt__running_status_frames=(
            " ${__crt__txtblu}    =>${__crt__txtrst} "
            " ${__crt__txtblu}     >${__crt__txtrst} "
        )

        __crt__move_cursor_to_top() {
            tput cuu "${__crt__task_count}"
            tput sc
        }

        __crt__move_cursor_below_tasks() {
            tput cud "${__crt__task_count}"
            tput sc
        }

        __crt__move_cursor_below_status() {
            __crt__move_cursor_below_tasks
        }

        __crt__draw_initial_tasks() {
            local i
            for (( i = 0; i < __crt__task_count; i++ )); do
                echo "        ${__crt__names[${i}]}"
            done
        }

        __crt__move_cursor_to_index() {
            local index=${1}
            [[ "${index}" -eq 0 ]] || tput cud "${index}"
        }

        __crt__draw_status() {
            local index=${1}
            local code=${2}
            tput rc
            __crt__move_cursor_to_index "${index}"
            __crt__draw_task "${code}"
            tput rc
        }

        __crt__draw_task() {
            local code=${1}
            if   [[ "${code}" == "running" ]]; then __crt__draw_running_status
            elif [[ "${code}" == "int"     ]]; then printf " ${__crt__txtred}%s${__crt__txtrst} " 'SIGINT'
            elif [[ "${code}" == "skip"    ]]; then printf " ${__crt__txtylw}%s${__crt__txtrst} " ' SKIP '
            elif [[ "${code}" == "0"       ]]; then printf " ${__crt__txtgrn}%s${__crt__txtrst} " '  OK  '
            else                                    printf " ${__crt__txtred}%s${__crt__txtrst} " 'FAILED'
            fi
        }

        __crt__draw_running_status() {
            # shellcheck disable=SC2059
            printf "${__crt__running_status_frames[${__crt__running_status_current_frame}]}"
        }

        __crt__draw_meta() {
            local index=${1}
            tput rc
            __crt__move_cursor_to_index "${index}"
            tput cuf 8  # move past status
            printf "%s ${__crt__txtbld}%s${__crt__txtrst}${__crt__txtclr}" "${__crt__names[${index}]}" "${__crt__meta[${index}]}"
            tput rc
        }

        __crt__update_running_status_frames() {
            local i
            for (( i = 0; i < __crt__task_count; i++ )); do
                if __crt__is_task_running "${i}"; then
                    __crt__draw_status "${i}" running
                fi
            done
            __crt__running_status_current_frame=$((
                (__crt__running_status_current_frame + 1) % ${#__crt__running_status_frames[@]}
            ))
        }

        __crt__indent() {
            sed 's/^/    /' "${@}"
        }

        __crt__print_failures() {
            cd "${__crt__status_dir}"
            local i
            for (( i = 0; i < __crt__task_count; i++ )); do
                if [[ "${__crt__codes[${i}]}" != '0' ]]; then
                    printf "\n['%s' failed with exit status %s]\n" "${__crt__names[${i}]}" "${__crt__codes[${i}]}"
                    __crt__indent "${i}"
                fi
            done
        }
    fi

    __crt__print_log_location() {
        if [[ "${__crt__final_status}" != "0" && "${CONCURRENT_DEPTH}" -eq 0 ]]; then
            printf '\nLogs for all tasks can be found in:\n    %s\n' "${CONCURRENT_LOG_DIR}/"
        fi
    }

    __crt__disable_echo() {
        # Disable local echo so the user can't mess up the pretty display.
        stty -echo
    }

    __crt__enable_echo() {
        # Enable local echo so user can type again. (Simply exiting the subshell
        # is not sufficient to reset this, which is surprising.)
        stty echo
    }

    __crt__hide_cursor() {
        tput civis
    }

    __crt__show_cursor() {
        tput cnorm
    }

    __crt__status_cleanup() {
        trap -- - INT  # no longer need special sigint handling
        __crt__move_cursor_below_status
        __crt__print_failures
        __crt__print_log_location
    }

    #
    # Signal Handling/General Cleanup
    #

    __crt__exit_by_signal() {
        # Proper sigint handling: http://www.cons.org/cracauer/sigint.html
        local signal=${1}
        # shellcheck disable=SC2064
        trap -- - "${signal}"    # reset the signal
        kill "-${signal}" -- $$  # re-raise the signal
        exit 255                 # don't resume the script
    }

    __crt__handle_exit() {
        rm -rf "${__crt__status_dir}"
        __crt__hide_failure __crt__restore_stdin_stream
        __crt__hide_failure __crt__enable_echo
        __crt__hide_failure __crt__show_cursor
    }

    __crt__handle_sigint() {
        CONCURRENT_LIMIT=-1
        __crt__interrupted=1
        __crt__clear_event_pipe
        __crt__cleanup_event_loop
        __crt__mark_all_running_tasks_as_interrupted
        __crt__run_event_loop
        __crt__status_cleanup
        __crt__stop_animation
        __crt__hide_failure wait
        __crt__exit_by_signal INT
    }

    # Keep track of how far we're nested inside concurrent instances.
    export CONCURRENT_DEPTH=${CONCURRENT_DEPTH:--1}
    (( CONCURRENT_DEPTH++ )) || :

    if [[ "${CONCURRENT_DEPTH}" -gt 0 ]]; then
        # If we're nested inside a running instance of concurrent, disable the
        # interactive statuses.
        __crt__enable_echo               () { :; }
        __crt__disable_echo              () { :; }
        __crt__draw_initial_tasks        () { :; }
        __crt__move_cursor_to_top        () { :; }
        __crt__move_cursor_below_status  () { :; }
        __crt__draw_status               () { :; }
        __crt__draw_meta                 () { :; }
        __crt__start_animation           () { :; }
        __crt__stop_animation            () { :; }
    fi

    export CONCURRENT_LOG_DIR=${CONCURRENT_LOG_DIR:-${PWD}/.logs/$(date +'%F@%T')}
    mkdir -p "${CONCURRENT_LOG_DIR}"

    __crt__disable_echo || __crt__error 'Must be run in the foreground of an interactive shell!'
    __crt__status_dir=$(mktemp -d "${TMPDIR:-/tmp}/concurrent.lib.sh.XXXXXXXXXXX")
    __crt__event_pipe="${__crt__status_dir}/event-pipe"
    __crt__clear_event_pipe

    trap -- __crt__handle_exit EXIT
    trap -- __crt__handle_sigint INT

    if __crt__is_dry_run; then
        # shellcheck disable=SC2016
        echo '>>> DRY RUN (concurrent): The "$CONCURRENT_DRY_RUN" environment variable is set. <<<'
    fi

    __crt__hide_cursor
    __crt__start_all_tasks
    __crt__wait_for_all_tasks
    __crt__show_cursor

    exit ${__crt__final_status}
)

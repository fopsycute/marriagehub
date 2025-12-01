

document.addEventListener("DOMContentLoaded", function() {
  // ✅ Selectors
  const thumbnails = document.querySelectorAll('.thumbnail-item');
  const mainImage = document.getElementById('main-product-image');
  const prevBtn = document.querySelector('.prev-image');
  const nextBtn = document.querySelector('.next-image');

  if (!thumbnails.length || !mainImage) return;

  let currentIndex = 0; // Track active image index

  // ✅ Function: Update Main Image + Active Thumbnail
  function updateMainImage(index) {
    const selected = thumbnails[index];
    if (!selected) return;

    const newImage = selected.getAttribute('data-image');
    mainImage.src = newImage;
    mainImage.setAttribute('data-zoom', newImage);

    thumbnails.forEach(t => t.classList.remove('active'));
    selected.classList.add('active');

    currentIndex = index;
  }

  // ✅ Thumbnail Click Event
  thumbnails.forEach((thumbnail, index) => {
    thumbnail.addEventListener('click', function(e) {
      e.preventDefault();
      updateMainImage(index);
    });
  });

  // ✅ Prev Button
  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      let newIndex = currentIndex - 1;
      if (newIndex < 0) newIndex = thumbnails.length - 1; // Loop back to last
      updateMainImage(newIndex);
    });
  }

  // ✅ Next Button
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      let newIndex = (currentIndex + 1) % thumbnails.length; // Loop forward
      updateMainImage(newIndex);
    });
  }

  // ✅ Initialize First Image
  updateMainImage(0);
});





// Voting handlers appended: question and answer votes
document.addEventListener('DOMContentLoaded', function(){

  // 🔥 Get your siteurl from hidden input
  var siteUrl = $('#siteurl').val(); 
  var base = siteUrl + "script/"; // always siteurl + script/

  async function apiGet(path){
    try { 
        const res = await fetch(base + path); 
        return res.ok ? res.json() : null; 
    }
    catch(e){ console.error(e); return null; }
  }

  async function apiPost(path, data){
    try { 
        const res = await fetch(base + path, { method: 'POST', body: data });
        return res.ok ? res.json() : null;
    }
    catch(e){ console.error(e); return null; }
  }

  // 🔥 helper: update icons depending on vote (1, -1, or 0)
  function updateVoteIcons(upBtn, downBtn, vote) {
      const upIcon = upBtn.querySelector('i');
      const downIcon = downBtn.querySelector('i');

      if (vote === 1) {
          upIcon.classList.add("bi-hand-thumbs-up-fill");
          upIcon.classList.remove("bi-hand-thumbs-up");

          downIcon.classList.add("bi-hand-thumbs-down");
          downIcon.classList.remove("bi-hand-thumbs-down-fill");
      } 
      else if (vote === -1) {
          downIcon.classList.add("bi-hand-thumbs-down-fill");
          downIcon.classList.remove("bi-hand-thumbs-down");

          upIcon.classList.add("bi-hand-thumbs-up");
          upIcon.classList.remove("bi-hand-thumbs-up-fill");
      } 
      else {
          upIcon.classList.add("bi-hand-thumbs-up");
          upIcon.classList.remove("bi-hand-thumbs-up-fill");

          downIcon.classList.add("bi-hand-thumbs-down");
          downIcon.classList.remove("bi-hand-thumbs-down-fill");
      }
  }

  // ----------------------- QUESTION VOTING -----------------------
  const upBtn = document.getElementById('question-upvote');
  const downBtn = document.getElementById('question-downvote');
  const scoreEl = document.getElementById('question-score');

  if (upBtn && downBtn && scoreEl) {
      const qid = upBtn.getAttribute('data-question-id');

      apiGet('user.php?action=get_votes&type=question&id=' + encodeURIComponent(qid))
      .then(d => {
          if (d && d.status === 'success') {
              scoreEl.textContent = d.score;
              updateVoteIcons(upBtn, downBtn, d.user_vote ?? 0);
          }
      });

      upBtn.addEventListener('click', async function(){
          const form = new FormData();
          form.append('action','vote');
          form.append('type','question');
          form.append('id', qid);
          form.append('vote', '1');

          const r = await apiPost('user.php', form);

          if (r && r.status==='success') {
              scoreEl.textContent = r.score;
              updateVoteIcons(upBtn, downBtn, 1);
          } else if (r?.message) alert(r.message);
      });

      downBtn.addEventListener('click', async function(){
          const form = new FormData();
          form.append('action','vote');
          form.append('type','question');
          form.append('id', qid);
          form.append('vote', '-1');

          const r = await apiPost('user.php', form);

          if (r && r.status==='success') {
              scoreEl.textContent = r.score;
              updateVoteIcons(upBtn, downBtn, -1);
          } else if (r?.message) alert(r.message);
      });
  }

  // ----------------------- ANSWER VOTING -----------------------
  document.body.addEventListener('click', async function(e){
      const up = e.target.closest('.comment-upvote');
      const down = e.target.closest('.comment-downvote');
      if (!up && !down) return;

      const isUp = !!up;
      const btn = up || down;
      const aid = btn.getAttribute('data-answer-id');

      const upButton = document.querySelector(`.comment-upvote[data-answer-id="${aid}"]`);
      const downButton = document.querySelector(`.comment-downvote[data-answer-id="${aid}"]`);

      const form = new FormData();
      form.append('action','vote');
      form.append('type','answer');
      form.append('id', aid);
      form.append('vote', isUp ? '1' : '-1');

        const r = await apiPost('user.php', form);
        if (r && r.status === 'success') {
          // optimistic update if server returned score
          const scoreElA = document.getElementById('answer-score-' + aid);
          if (scoreElA && typeof r.score !== 'undefined') scoreElA.textContent = r.score;

          // fetch fresh vote state from server to ensure up/down counts and user_vote are accurate
          try {
            const fresh = await apiGet('user.php?action=get_votes&type=answer&id=' + encodeURIComponent(aid));
            if (fresh && fresh.status === 'success') {
              if (scoreElA && typeof fresh.score !== 'undefined') scoreElA.textContent = fresh.score;
              const upCount = document.getElementById('answer-up-' + aid);
              const downCount = document.getElementById('answer-down-' + aid);
              if (upCount && typeof fresh.upvotes !== 'undefined') upCount.textContent = fresh.upvotes;
              if (downCount && typeof fresh.downvotes !== 'undefined') downCount.textContent = fresh.downvotes;
              updateVoteIcons(upButton, downButton, fresh.user_vote ?? 0);
            } else {
              // fallback: update icons based on the action we just performed
              updateVoteIcons(upButton, downButton, isUp ? 1 : -1);
            }
          } catch(e) {
            // on any error, at least update icons optimistically
            updateVoteIcons(upButton, downButton, isUp ? 1 : -1);
          }
        } else if (r?.message) alert(r.message);
  });

  // Initialize answer vote icons/counts on page load so filled icons show for user's previous votes
  async function initAnswerVotes(){
    try{
      const ups = document.querySelectorAll('.comment-upvote');
      for (const upEl of ups){
        const aid = upEl.getAttribute('data-answer-id');
        if (!aid) continue;
        const downEl = document.querySelector('.comment-downvote[data-answer-id="' + aid + '"]');
        try{
          const d = await apiGet('user.php?action=get_votes&type=answer&id=' + encodeURIComponent(aid));
          if (d && d.status === 'success'){
            // update icons
            updateVoteIcons(upEl, downEl, d.user_vote ?? 0);
            // update counts if provided
            const scoreEl = document.getElementById('answer-score-' + aid);
            if (scoreEl && typeof d.score !== 'undefined') scoreEl.textContent = d.score;
            const upCount = document.getElementById('answer-up-' + aid);
            const downCount = document.getElementById('answer-down-' + aid);
            if (upCount && typeof d.upvotes !== 'undefined') upCount.textContent = d.upvotes;
            if (downCount && typeof d.downvotes !== 'undefined') downCount.textContent = d.downvotes;
          }
        } catch(e){ /* ignore per-answer errors */ }
      }
    } catch(e){ console.error('initAnswerVotes error', e); }
  }
  // run initialization
  try{ initAnswerVotes(); } catch(e){ console.error(e); }

  // Answers are rendered server-side in PHP; client-side rendering/fetching removed.

});




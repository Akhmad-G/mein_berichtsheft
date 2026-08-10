<form class="report-form" method="POST" action="#">
  @csrf
  
  <hr>
  <div class="report-meta">
    <div class="form-field">
      <label for="ausbildungsnachweis_nummer">Ausbildungsnachweis Nummer</label>
      <input type="text" name="ausbildungsnachweis_nummer" id="ausbildungsnachweis_nummer">
    </div>

    <div class="form-field">
      <label for="datum">Datum</label>
      <input type="date" name="datum" id="datum">
    </div>

    <div class="form-field">
      <label for="wochentag">Wochentag</label>
      <input type="text" name="wochentag" id="wochentag">
    </div>

    <div class="form-field">
      <label for="name">Name</label>
      <input type="text" name="name" id="name">
    </div>

    <div class="form-field">
      <label for="ausbildungsberuf">Ausbildungsberuf</label>
      <input type="text" name="ausbildungsberuf" id="ausbildungsberuf">
    </div>

    <div class="form-field">
      <label for="betrieb">Betrieb</label>
      <input type="text" name="betrieb" id="betrieb">
    </div>

    <div class="form-field">
      <label for="abteilung">Abteilung</label>
      <input type="text" name="abteilung" id="abteilung">
    </div>

    <div class="form-field">
      <label for="ausbildungsjahr">Ausbildungsjahr</label>
      <input type="number" name="ausbildungsjahr" id="ausbildungsjahr">
    </div>

    <div class="form-field">
      <label for="ausbildungswoche">Ausbildungswoche</label>
      <input type="week" name="ausbildungswoche" id="ausbildungswoche">
    </div>    
  </div>
  <hr>
  
  <div class="report-body">
    <div class="form-field form-field--activities">
      <label for="taetigkeiten">Tätigkeiten</label>
      <textarea name="taetigkeiten" id="taetigkeiten"></textarea>
    </div>
    <div class="form-field form-field--learned">
      <label for="gelernt">Was habe ich gelernt?</label>
      <textarea name="gelernt" id="gelernt"></textarea>
    </div>
    <div class="form-field form-field--problems">
      <label for="probleme">Besondere Ereignisse / Probleme</label>
      <textarea name="probleme" id="probleme"></textarea>
    </div>
  </div>
  
  <div>
    <button type="submit">Tagesbericht erstellen</button>
{{--    oder Tagesbericht bearbeiten--}}
  </div>

  


</form>